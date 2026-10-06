<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Kesalahan "yang wajar" saat mencatat presensi (QR salah, sudah absen, dsb.).
 * Controller menangkapnya dan mengubahnya menjadi respons JSON yang ramah.
 */
class PresensiException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 422)
    {
        parent::__construct($message);
    }
}
