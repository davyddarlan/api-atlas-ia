<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestMatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class TokenPattern implements RequestMatcherInterface
{
    public function matches(Request $request): bool
    {
        $uri = $request->getRequestUri();
        $splitUri = explode('/', $uri);
        $lengthSplitParts = count($splitUri);

        if ($splitUri[1] == 'api') {
            foreach($splitUri as $token) {
                if ($token == 'public') {
                    return false;
                }
            }
        }

        return true;
    }
}