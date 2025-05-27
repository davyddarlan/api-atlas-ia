<?php

namespace App\Service;

use \Exception;

class PersistMetaData
{
    private $exif;
    private $metadados = [];

    public function setExif(string $path): self
    {
        $this->exif = exif_read_data($path, 'IFD0, EXIF', true, false);
        $this->fillVector();
        
        return $this;
    }

    public function getExif(): array
    {
        return $this->metadados;
    }

    public function hasExif(): bool
    {
        if ($this->exif) {
            return true;
        }

        return false;
    }

    public function hasExifTag(): bool
    {
        if ($this->hasExif() && array_key_exists('EXIF', $this->exif)) {
            return true;
        }

        return false;
    }

    public function fillVector(): self
    {
        if ($this->hasExifTag()) {
            foreach ($this->exif['EXIF'] as $key => $value) {
                $this->metadados[] = [
                    'nome' => $key,
                    'valor' => $value,
                ];
            }
        }

        return $this;
    }
}