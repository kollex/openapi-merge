<?php

declare(strict_types=1);

namespace Mthole\OpenApiMerge\Console\Command;

use Mthole\OpenApiMerge\FileHandling\Exception\IOException;
use Mthole\OpenApiMerge\FileHandling\File;
use Mthole\OpenApiMerge\FileHandling\SpecificationFile;
use Mthole\OpenApiMerge\OpenApiMergeInterface;
use Mthole\OpenApiMerge\Writer\DefinitionWriterInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class MergeCommand extends Command
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     *
     * @var string
     */
    protected static $defaultDescription = 'Merge multiple OpenAPI definition files into a single file';
    public const COMMAND_NAME = 'openapi:merge';

    private OpenApiMergeInterface $merger;

    public function __construct(
        OpenApiMergeInterface $openApiMerge,
        private readonly DefinitionWriterInterface $definitionWriter,
    ) {
        parent::__construct(self::COMMAND_NAME);

        $this->merger = $openApiMerge;
    }

    protected function configure(): void
    {
        $this
            ->setHelp(
                <<<'HELP'
                Usage:
                    basefile.yml additionalFileA.yml additionalFileB.yml [...] > combined.yml

                Allowed extensions:
                    Only .yml, .yaml and .json files are supported

                Outputformat:
                    The output format is determined by the basefile extension.
                HELP,
            )
            ->addArgument('basefile', InputArgument::REQUIRED)
            ->addArgument('additionalFiles', InputArgument::IS_ARRAY)
            ->addOption(
                'outputfile',
                'o',
                InputOption::VALUE_OPTIONAL,
                'Defines the output file for the result. Defaults the result will printed to stdout',
            );
    }

    /** @throws IOException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $baseFile = $input->getArgument('basefile');
        $additionalFiles = $input->getArgument('additionalFiles');

        if (!\is_string($baseFile) || !\is_array($additionalFiles)) {
            throw new \Exception('Invalid arguments given');
        }

        $mergedResult = $this->merger->mergeFiles(
            new File($baseFile),
            ...array_map(
                static fn (string $file): File => new File($file),
                $additionalFiles,
            ),
        );

        $outputFileName = $input->getOption('outputfile');
        if (\is_string($outputFileName)) {
            touch($outputFileName);
            $outputFile = new File($outputFileName);
            $specificationFile = new SpecificationFile(
                $outputFile,
                $mergedResult->getOpenApi(),
            );
            file_put_contents(
                $outputFile->getAbsolutePath(),
                $this->definitionWriter->write($specificationFile),
            );
            $output->writeln(sprintf('File successfully written to %s', $outputFile->getAbsolutePath()));
        } else {
            $output->write($this->definitionWriter->write($mergedResult));
        }

        return Command::SUCCESS;
    }
}
