<?php

namespace Rogiermulders\Bam\Console;
use Illuminate\Console\Command;
use Rogiermulders\Bam\Bam;

class BamConsole extends Command
{
    protected $signature = 'rogiermulders:bam {url?}';

    protected $description = 'One click to Controller::method';

    public function handle(Bam $bam): int
    {
        /**
         * User input
         */
        if (!$url = $this->argument('url')) {
            $url = $this->ask('What is the url?');
        }

        // Remove query string http://foo.com/bar?baz=1 -> http://foo.com/bar
        $temp = explode('?', $url);
        $url = $temp[0];

        // Remove the first 3 parts of the url http://foo.com/bar/baz/boo -> bar/baz/boo
        $temp = explode('/', $url);
        $givenRoute = implode('/', array_slice($temp, 3));

        // Now get the matched route
        $matchedRoute = $bam->getMachedRoute($givenRoute);

        // Ask for the method when we have multiple
        $methods = array_keys($matchedRoute ?? []);
        if (count($methods) === 1) {
            // Not much choice
            $method = $methods[0];
        } else {
            $method = $this->choice('Which method do you want to open?', $methods, 0);
        }

        $bam->openControllerAndJumpToLine($matchedRoute,$method);

        return 0;

    }

}
