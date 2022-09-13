<?php

declare(strict_types=1);

namespace App\Http;

use Illuminate\Http\{JsonResponse, Response};
use Illuminate\Validation\ValidationException;

class JsonResponseFactory
{
    /**
     * Kaldes på successful request.
     *
     * Returnere et JSON response.
     */
    public static function success(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return self::response($message, $data, $statusCode);
    }

    /**
     * Kaldes i andre funktioner for at give mere
     * definerbare fejl for letlæselighed i koden.
     *
     * Returnere et JSON response.
     */
    public static function error(
        mixed $data = null,
        ?string $message = null,
        int $statusCode = Response::HTTP_BAD_REQUEST
    ): JsonResponse {
        return self::response($message, $data, $statusCode);
    }

    public static function created(mixed $data = null, string $message = 'Created'): JsonResponse
    {
        return self::response($message, $data, Response::HTTP_CREATED);
    }

    /**
     * Kaldes hvis din request er unauthorized
     *
     * Returnere en fejl JSON response.
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return self::error(null, $message, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Kaldes hvis din request er forbidden/unauthorized.
     *
     * Returnere en fejl JSON response.
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return self::error(null, $message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Kaldes hvis der ikke kan findes et response/resource
     *
     * Returnere en fejl JSON response.
     */
    public static function notFound(string $message = 'Not Found'): JsonResponse
    {
        return self::error(null, $message, Response::HTTP_NOT_FOUND);
    }

    /**
     * Kaldes hvis der er en fejl med noget klient data.
     *
     * Returnere en fejl JSON response.
     */
    public static function badRequest(string $message = 'Bad Request'): JsonResponse
    {
        return self::error(null, $message, Response::HTTP_BAD_REQUEST);
    }

    /**
     * Kaldes hvis der sker en server error med ens request
     *
     * Returnere en fejl JSON response.
     */
    public static function serverError(string $message = 'Server Error'): JsonResponse
    {
        return self::error(null, $message, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Kaldes i tilfælde af en validerings-fejl.
     *
     * Returnere en fejl JSON response.
     */
    public static function validationError(ValidationException $e): JsonResponse
    {
        return self::error(['errors' => $e->errors()], 'Validation Error');
    }

    /**
     * Privat metode der kaldes af de andre metoder i klassen.
     *
     * Sætter data op ordentligt til returning til klienten.
     */
    private static function response(
        ?string $message = null,
        mixed $data = null,
        int $statusCode = 200
    ): JsonResponse {
        // Make sure there is 1 and only 1 data key in response.
        if (!is_array($data) || (is_array($data) && !isset($data['data']))) {
            $data = ['data' => $data];
        }

        // Returnere et JSON response med en message, data og status-kode.
        return response()->json(
            [
                'message' => $message,
                'success' => $statusCode < 300,
                ...$data
            ],
            $statusCode
        );
    }
}
