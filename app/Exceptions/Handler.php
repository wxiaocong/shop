<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{

    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = array(
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Contracts\Validation\ValidationException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Http\Exception\HttpResponseException::class,
    );

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof TokenMismatchException) {
            return redirect()->back()->with('message', '页面已经过期，请刷新页面后登录。')->withInput();
        }

        if ($e instanceof HttpException
            || $e instanceof HttpResponseException) {
            return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            if ($request->expectsJson()) {
                return $this->invalidJson($request, $e);
            } else {
                return parent::invalid($request, $e);
            }
        }

        // Other exceptions will be responsed with 500 page.
        $errorCode = $e->getCode();
        if (!is_int($errorCode)) {
            $errorCode = 999;
        }

        $errorPage = 400;
        //如果是前台调用,返回500错误页面
        if ($request->route()->getAction()['namespace'] == 'App\\Http\\Controllers\\Users') {
            $errorPage = 500;
        }

        return response()->view(
            'errors.' . $errorPage,
            array(
                'exception' => new Exception(
                    '系统出现问题，请返回上一页或者重试。
                    如需帮助，请联系客服。',
                    $errorCode
                ),
            ),
            $errorPage
        );
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        $errorList = array();
        $errors    = $exception->errors();
        foreach ($errors as $error) {
            $errorList[] = $error;
        }
        return response()->json(
            array(
                'code'     => 500,
                'messages' => $errorList,
                'url'      => '',
            )
        );
    }
}
