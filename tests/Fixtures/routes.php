<?php

/*
 * This file is part of the Sanctum ORM project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Kilip\DoctrineSanctum\Manager\TokenManagerInterface;

Route::middleware('auth:sanctum')
    ->get('/api/user', function (Request $request) {
        return response()->json($request->user());
    });

Route::post('/api/login', function (TokenManagerInterface $tokenManager, Request $request) {
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
        'device'   => 'required',
    ]);

    $user = $tokenManager->findUserBy(['email' => $request->get('email')]);

    if (!$user || !Hash::check($request->get('password'), $user->getPassword())) {
        throw ValidationException::withMessages(['email' => ['The provided credentials are incorrect.']]);
    }

    $token = $tokenManager->createToken($user, $request->get('device'));

    return response()->json(['token' => $token->plainTextToken]);
});
