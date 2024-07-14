<?php

namespace App\Console\Commands;

use App\DTO\ClasMakerWebhookDTO;
use App\UseCases\ClasMaker\SendExamResultToBitrixUseCase;
use Exception;
use Illuminate\Console\Command;

class ImportCsvToBitrixCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-csv-to-bitrix-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        SendExamResultToBitrixUseCase $useCase
    )
    {
        $file = fopen(storage_path('/app/data.csv'), 'r');

        $data = [];
        while(! feof($file))
        {
            $data[] = fgetcsv($file);
        }
        fclose($file);

        foreach($data as $key => $d) {
            if ($key >= 7
                and isset($d[0])
                and isset($d[1])
                and isset($d[2])
                and isset($d[4])
                and isset($d[5])
            ) {
                try {
                    $newData[] = [
                        'name' => $d[0],
                        'last_name' => $d[1],
                        'email' => $d[2],
                        'subject' => $d[4],
                        'percentage' => $d[5],
                        'date' => $d[11],
                        'passport' => $d[17],
                    ];
                } catch (Exception $e) {
                    dd($d);
                }
            }
        }

        $collection = collect($newData);

        $collection = $collection->skip(135+7+10+8+55+89+6+112+3+101);
        $this->output->progressStart($collection->count());

        $collection->map(callback: function ($item) use ($useCase) {
            sleep(8);
            $dto = new ClasMakerWebhookDTO(
                testName: $item['subject'],
                firstName: $item['name'],
                lastName: $item['last_name'],
                email: $item['email'],
                percentage: (int) $item['percentage'],
                passportNumber: $item['passport']
            );

            $dto->setDate($item['date']);

            $result = $useCase->execute($dto);

            $this->output->progressAdvance();
            $this->output->text($result);

        });

        $this->output->progressFinish();
    }
}
