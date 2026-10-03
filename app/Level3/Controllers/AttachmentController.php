<?php

namespace App\Level3\Controllers;

use App\Level3\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController
{
    public function store(Request $request, $ticketId)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $dir = storage_path('app/uploads');

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
            chmod($dir, 0777);
        }

        $name = $file->getClientOriginalName();
        $file->move($dir, $name);

        $attachment = Attachment::create([
            'ticket_id' => $ticketId,
            'original_name' => $name,
            'stored_path' => $dir . '/' . $name,
            'size' => $file->getSize(),
            'mime_type' => $file->getClientMimeType(),
            'is_admin' => $request->input('is_admin', false),
        ]);

        return response()->json($attachment, 201);
    }

    public function storeRaw(Request $request, $ticketId)
    {
        $attachment = Attachment::create($request->all());

        return response()->json($attachment, 201);
    }

    public function download(Request $request)
    {
        $path = storage_path('app/uploads/' . $request->query('file'));

        return response()->download($path);
    }
}
