<?php



namespace Hbv\Minio\Controllers;

use \Illuminate\Http\Request;

class MainController
{
    public function singleUpload(Request $request): string
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,jpg,png,gif',
                'entity_id' => 'required',
                'entity_type' => 'required',
                'section' => 'required',
                'alt' => 'nullable',
                'description' => 'nullable',
            ]);
            return MinioControl::upload($request->post(), $request->file('file'));
        } catch (\Exception $exception) {
            return response()->json(['result' => 'false', 'messages' => $exception->getMessage(), 'data' => null], 400);
        }
    }

    public function getLink($name): string
    {
        return MinioControl::getTemporaryLink(sprintf('%s/%s', 'public', $name));
    }
}
