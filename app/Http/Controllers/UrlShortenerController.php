<?php

namespace App\Http\Controllers;

use App\Http\Requests\DecodeUrlRequest;
use App\Http\Requests\EncodeUrlRequest;
use App\Services\UrlShortener;
use Exception;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;


class UrlShortenerController extends Controller
{
    private string $SHORTEN_METHOD = 'memory'; // Supported  method memory | database
    const SUPPORTED_SHORTEN_METHODS = ['memory', 'database'];

    /**
     * Initializes the UrlShortener service globally, making it accessible throughout the controller.
     *
     * This constructor also ensures that the session is started if it hasn't been already.
     * Additionally, it initializes the URL map in the session if it is not already set.
     *
     * @param UrlShortener $urlShortener The URL shortening service to be used within the controller.
     */
    public function __construct(public UrlShortener $urlShortener)
    {
        // Check for session, start if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize URL map if not initialized
        if (!isset($_SESSION['url_map'])) {
            $_SESSION['url_map'] = [];
        }
    }

    /**
     * SHORTEN_METHOD: Determines the storage method for the shortened URL.
     * - If set to 'memory', the URL will be stored temporarily in memory.
     * - If set to 'database', a record will be created in the database.
     *
     * @param EncodeUrlRequest $request
     * url - The request containing the URL to be shortened. Validation will ensure it is a valid URL.
     * short_method - (optional) Sets the method of storing the URLs
     * @return ResponseFactory|Application|JsonResponse|Response The response containing the shortened URL or an error message.
     */
    public function encode(EncodeUrlRequest $request)
    {
        // Check if the request wants JSON output, if not then return back
        if (!$request->wantsJson()) return response('Not Acceptable', 406);

        if ($request->short_method && in_array($request->short_method, self::SUPPORTED_SHORTEN_METHODS)) {
            $this->SHORTEN_METHOD = $request->short_method;
        }

        try {
            if ($this->SHORTEN_METHOD == 'memory') {
                $shortUrl = $this->urlShortener->encodeMemory($request->url);
            } else {
                $shortUrl = $this->urlShortener->encode($request->url);
            }

            return response()->json([
                'input_url' => $request->url,
                'short_url' => $shortUrl,
                'short_method' => $this->SHORTEN_METHOD
            ], 201);
        } catch (Exception) {
            return response()->json(['error' => 'Something went wrong, please try again later'], 500);
        }
    }

    /**
     * SHORTEN_METHOD: Specifies how the original URL is retrieved.
     * - If set to 'memory', the original URL is fetched from temporary memory storage.
     * - If set to 'database', the original URL is retrieved from the database.
     *
     * @param DecodeUrlRequest $request
     * short_url - The request containing the short URL to be decoded. Validation will ensure it is a valid URL.
     * short_method - (optional) Sets the method of storing the URLs
     * @return ResponseFactory|Application|JsonResponse|Response The response containing the original URL or an error message.
     */
    public function decode(DecodeUrlRequest $request)
    {
        // Check if the request wants JSON output, if not then return back
        if (!$request->wantsJson()) return response('Not Acceptable', 406);

        if ($request->short_method && in_array($request->short_method, self::SUPPORTED_SHORTEN_METHODS)) {
            $this->SHORTEN_METHOD = $request->short_method;
        }

        try {
            if ($this->SHORTEN_METHOD == 'memory')
                $originalUrl = $this->urlShortener->decodeMemory($request->short_url);
            else
                $originalUrl = $this->urlShortener->decode($request->short_url);

            if ($originalUrl == null)
                return response()->json(['error' => 'Shorten url not found'], 404);

            return response()->json([
                'short_url' => $request->short_url,
                'url' => $originalUrl,
                'short_method' => $this->SHORTEN_METHOD
            ], 201);
        } catch (Exception) {
            return response()->json(['error' => 'Something went wrong, please try again later'], 500);
        }
    }
}
