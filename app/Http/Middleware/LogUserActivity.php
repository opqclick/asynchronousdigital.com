<?php

namespace App\Http\Middleware;

use App\Models\UserActivity;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (!auth()->check()) {
            return $response;
        }

        // Only log certain methods
        $method = $request->method();
        if (!in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return $response;
        }

        // Skip logging for certain routes
        $skipRoutes = ['logout', 'login', 'register', 'password', 'telescope', 'livewire', '_debugbar'];
        $path = $request->path();
        foreach ($skipRoutes as $skip) {
            if (Str::contains($path, $skip)) {
                return $response;
            }
        }

        // Only log successful responses (2xx or 3xx status codes for redirects)
        $statusCode = $response->getStatusCode();
        if ($statusCode < 200 || $statusCode >= 400) {
            return $response;
        }

        // Determine action and description
        $action = $this->determineAction($request);
        $description = $this->generateDescription($request, $action);
        $modelInfo = $this->extractModelInfo($request);

        // Log the activity
        try {
            UserActivity::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model' => $modelInfo['model'],
                'model_id' => $modelInfo['id'],
                'description' => $description,
                'changes' => null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            \Log::info('Activity logged: ' . $description . ' by user ' . auth()->id());
        } catch (\Exception $e) {
            // Silently fail to not disrupt the application
            \Log::error('Failed to log user activity: ' . $e->getMessage());
        }

        return $response;
    }

    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        if ($method === 'POST' && !Str::contains($path, ['update', 'delete'])) {
            return 'create';
        } elseif (in_array($method, ['PUT', 'PATCH']) || Str::contains($path, 'update')) {
            return 'update';
        } elseif ($method === 'DELETE' || Str::contains($path, ['delete', 'destroy'])) {
            return 'delete';
        }

        return 'action';
    }

    private function generateDescription(Request $request, string $action): string
    {
        $path = $request->path();
        $segments = explode('/', $path);
        
        // Get the resource name (e.g., 'salaries', 'users', 'projects')
        $resource = null;
        foreach (['users', 'salaries', 'projects', 'tasks', 'clients', 'teams', 'invoices', 'payments', 'services', 'testimonials'] as $r) {
            if (Str::contains($path, $r)) {
                $resource = ucfirst(Str::singular($r));
                break;
            }
        }

        if (!$resource) {
            $resource = 'Record';
        }

        $actionText = ucfirst($action);
        
        return "{$actionText}d {$resource} via {$request->method()} request";
    }

    private function extractModelInfo(Request $request): array
    {
        $path = $request->path();
        $segments = explode('/', $path);
        
        $models = [
            'users' => 'User',
            'salaries' => 'Salary',
            'projects' => 'Project',
            'tasks' => 'Task',
            'clients' => 'Client',
            'teams' => 'Team',
            'invoices' => 'Invoice',
            'payments' => 'Payment',
            'services' => 'Service',
            'testimonials' => 'Testimonial',
        ];

        $model = null;
        $id = null;

        foreach ($models as $plural => $singular) {
            if (Str::contains($path, $plural)) {
                $model = $singular;
                
                // Try to extract ID from path
                $key = array_search($plural, $segments);
                if ($key !== false && isset($segments[$key + 1]) && is_numeric($segments[$key + 1])) {
                    $id = (int) $segments[$key + 1];
                }
                break;
            }
        }

        return ['model' => $model, 'id' => $id];
    }
}
