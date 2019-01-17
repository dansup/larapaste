<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Paste;
use \Carbon\Carbon;

class PasteController extends Controller
{
    public function show(Request $request, $key)
    {
      $paste = Paste::valid()->where('key', $key)->firstOrFail();
      return view('paste.show', compact('paste'));
    }

    public function showJson(Request $request, $key)
    {
      $paste = Paste::valid()->where('key', $key)->firstOrFail();
      return response()->json($paste, 200, [], JSON_PRETTY_PRINT);
    }

    public function create(Request $request)
    {
      $this->validate($request, [
        'data'      => 'required',
        'metadata'  => 'required',
        'ttl'   => 'required|integer|min:15|max:43800'
      ]);

      $data = $request->input('data');
      $metadata = $this->validateJson($request->input('metadata'));
      $ttl = $request->input('ttl');

      $paste = new Paste;
      $paste->key = str_random(12);
      $paste->data = $data;
      $paste->ip = $request->ip();
      $paste->metadata = json_encode($metadata);
      $paste->is_encrypted = true;
      $paste->ttl = $ttl;
      $paste->expires_at = Carbon::now()->addMinutes($ttl);
      $paste->save();

      return $paste;
    }

    public function validateJson($json)
    {
      // todo: more strict mode/theme validation
      $whitelisted = [];
      $whitelisted['mode'] = isset($json['mode']) ? $json['mode'] : 'ace/mode/text';
      $whitelisted['theme'] = isset($json['theme']) ? $json['theme'] : 'ace/theme/solarized_dark';
      $whitelisted['title'] = isset($json['title']) ? $json['title'] : null;
      return $whitelisted;
    }
}
