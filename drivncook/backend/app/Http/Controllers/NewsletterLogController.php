<?php

namespace App\Http\Controllers;

use App\Models\NewsletterLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\Collection;

class NewsletterLogController extends Controller
{
    public function index(): Collection
    {
        return NewsletterLog::with('customer')->latest('sent_at')->get();
    }

    public function store(Request $request): NewsletterLog
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sent_at'     => 'required|date',
            'subject'     => 'required|string|max:255',
            'content'     => 'nullable|string',
        ]);

        return NewsletterLog::create($validated);
    }
}
