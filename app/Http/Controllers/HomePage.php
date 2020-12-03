<?php


namespace App\Http\Controllers;


class HomePage extends Controller
{
    public function index()
    {
        // Generate demo data
        $demoData = [
            [2, 10],
            [3, 20],
            [4, 30],
            [5, 40],
            [6, 50],
        ];

        return view('home', [
            'page'     => 'Homepage!',
            'demoData' => $demoData,
        ]);
    }
}