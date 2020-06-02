<?php

namespace Khalidmsheet\Ktoken\Middleware;

use App\User;
use Carbon\Carbon;
use Closure;
use Khalidmsheet\Ktoken\Ktoken;

class KTokenAuthMiddleware
{

    /**
     * @var Ktoken
     */
    private $ktoken;

    public function __construct()
    {
        $this->ktoken = new Ktoken();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        $tokenData = $this->ktoken->decrypt($token);

        if ( !$token )
            return response()->json([
                'message' => 'Unauthenticated'
            ], 403);


        if ( !$tokenData )
            return response()->json([
                'message' => 'invalid_token'
            ]);


        if ( $this->ktoken->find($tokenData->kti)['revoked'] || Carbon::parse($tokenData->data->expires)->isBefore(Carbon::now()) )
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);


        $request->setUserResolver(function () use ($tokenData) {
            $model = config('ktoken.auth_model', User::class);
            $user = ( new $model )->find($tokenData->data->id);

            auth()->setUser($user);

            return $user;
        });

        return $next($request);
    }
}
