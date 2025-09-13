<?php

declare(strict_types=1);
namespace App\Services;

use Box\Spout\Writer\Common\Creator\Style\StyleBuilder;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

class Excel {

	public function export(array $data, string $filename): void{
		ob_clean();
		$writer = WriterEntityFactory::createXLSXWriter();
		$defaultStyle = (new StyleBuilder())
                ->setFontName('Arial')
                ->setFontSize(11)
                ->build();

		$writer->setDefaultRowStyle($defaultStyle)->openToBrowser($filename);
		foreach($data as $key => $content){
			foreach ($content as &$value) {
				$value = !!$value ? str_replace('<br>', "\n", $value) : $value;
				$value = mb_convert_encoding($value, 'UTF-8', 'auto');
			}
			$values = array_values($content);
			$rowFromValues = WriterEntityFactory::createRowFromArray($values);
			$writer->addRow($rowFromValues);
		}
		$writer->close();
	}
}