<?php

use Illuminate\Support\Facades\Route;
use Barryvdh\DomPDF\Facade\Pdf;



Route::get('/test-pdf', function () {
    $pdf = Pdf::loadHtml('<h1>PDF fonctionne ✅</h1>');
    return $pdf->stream('test.pdf');
});

