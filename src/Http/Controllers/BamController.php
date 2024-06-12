<?php

namespace Rogiermulders\Bam\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Rogiermulders\Bam\Bam;

class BamController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    function bam(Request $request): JsonResponse
    {

        $request->validate([
            'url' => 'required|string|max:255',
            'method' => 'required|string|in:GET,POST,PUT,DELETE,PATCH,OPTIONS,HEAD'
        ]);

        if(config('app.env') !== 'DEV'){
            abort(403, 'This route is only available in DEV environment');
        }

        $url = $request->get('url');
        $method = ($request->get('method') === 'GET' ? 'GET,HEAD' : $request->get('method'));

        $matchedRoute = Bam::getMachedRoute($url);
        Bam::openControllerAndJumpToLine($matchedRoute, $method);

        return response()->json('ok');
    }

}
