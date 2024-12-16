<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment('local');

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal ||
                   $entry->isReportableException() ||
                   $entry->isFailedRequest() ||
                   $entry->isFailedJob() ||
                   $entry->isScheduledTask() ||
                   $entry->hasMonitoredTag() ||
                   $this->shouldLogSuccessfulRequest($entry); // Gọi phương thức kiểm tra request
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    protected function shouldLogSuccessfulRequest(IncomingEntry $entry): bool
    {
        if ($entry->type === 'request') {
            $request = $entry->content;
            $method = $request['method'] ?? '';
            // $statusCode = $request['response_status'] ?? 0;

            // Chỉ log các request POST, PUT, DELETE với status 200
            if (in_array($method, ['POST', 'PUT', 'DELETE'])) {
                return true;
            }
        }

        return false;
    }
    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            if (!$user) {
                try {
                    $user = Auth::user();
                } catch (\Exception $e) {
                    return false;
                }
            }
            
            return in_array($user->email, [
                'test@example.com',
            ]); // Giả sử bạn có phương thức hasRole()
        });
    }
}
