<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    // 問い合わせ一覧
    public function index(): View
    {
        $contacts = Contact::latest()->paginate(20);

        return view('admin.contacts.index', compact('contacts'));
    }

    // 問い合わせ詳細
    public function show(Contact $contact): View
    {
        return view('admin.contacts.show', compact('contact'));
    }

    // 問い合わせ更新
    public function updateStatus(Request $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'integer', 'in:0,1,2'],
        ]);

        $contact->update(['status' => $validated['status']]);
            
        return redirect()->route('admin.contacts.show', $contact)
            ->with('success', 'ステータスを更新しました。');
    }
}
