<?php

namespace Rogiermulders\Bam;

use Exception;
use Illuminate\Support\Facades\Route;

class Bam
{

    /**
     * @param $url
     * @return array|mixed
     * @throws Exception
     */
    static public function getMachedRoute($url): mixed
    {
        // ================================================================================ //
        // Get all routes and make the $search array like so:
        //  $search['api/allyourz/*/media-by-entityid'] = [
        //      'GET,HEAD' => 'App\Http\Controllers\Api\Backend\MediaController@byEntityId',
        //      'POST' => 'App\Http\Controllers\Api\Backend\MediaController@save'
        //  ];
        // ================================================================================ //
        $routes = Route::getRoutes();
        foreach ($routes->getRoutes() as $route) {
            // To pattern for fnmatch
            $routeToPattern = preg_replace('/\{(.*?)}/s', '*', $route->uri);
            $search[$routeToPattern][implode(',', $route->methods)] = $route->action['controller'] ?? '';
        }

        // Need two loops cuz one route can have multiple methods match the route
        $matchedRoute = [];
        foreach ($search ?? [] as $key => $value) {
            if (fnmatch($key, $url)) {
                $matchedRoute = $value;
                break;
            }
        }
        if (!count($matchedRoute)) {
            if(app()->runningInConsole()){
                die("Route not found\n\n");
            } else {
                abort('Route not found');
            }
        }

        return $matchedRoute;

    }

    /**
     * @param $matchedRoute
     * @param $method
     * @return void
     */
    static public function openControllerAndJumpToLine($matchedRoute, $method): void
    {
        // To path and function name
        $classNameAndFunction = explode('@', $matchedRoute[$method]);

        // Flip the path and lowercase the first letter App/Etc... -> app/Etc..
        $fileName = lcfirst(str_replace('\\', '/', $classNameAndFunction[0]) . '.php');
        $functionName = $classNameAndFunction[1];

        // Find the line number
        $lines = explode("\n", file_get_contents(base_path() . '/' . $fileName));
        foreach ($lines as $i => $line) {
            if (str_contains($line, " $functionName(")) {
                break;
            }
        }

        $lineNumber = ($i ?? 0) + 1;
        $command = "phpstorm --line $lineNumber " . base_path() . "/$fileName >/dev/null";
        shell_exec($command);

    }

}
