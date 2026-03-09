<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ISU-Flow - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 1rem;
            padding: 3rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .logo h1 {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .logo p {
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: rgba(255, 255, 255, 0.8);
        }
        
        .form-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .error {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
            font-size: 0.9rem;
        }
        
        .demo-info {
            background: rgba(59, 130, 246, 0.1);
            color: #2563eb;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-top: 1.5rem;
            border: 1px solid rgba(59, 130, 246, 0.2);
            font-size: 0.85rem;
        }
        
        .demo-info h4 {
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        .demo-info ul {
            margin-left: 1rem;
            margin-top: 0.5rem;
        }
        
        .demo-info li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>ISU-Flow</h1>
            <p>Leave Management System</p>
        </div>
        
        @if ($errors->any())
            <div class="error">
                <div><strong>Error:</strong></div>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <input type="hidden" name="remember" value="true">
            
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input id="email" type="email" name="email" class="form-input" 
                       value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>
            
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input id="password" type="password" name="password" class="form-input" required autocomplete="current-password">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>
        
        <div class="demo-info">
            <h4>Demo Accounts</h4>
            <ul>
                <li><strong>Admin:</strong> admin@isu.edu.ph / password</li>
                <li><strong>HR:</strong> hr@isu.edu.ph / password</li>
                <li><strong>Employee:</strong> juan.delacruz@isu.edu.ph / password</li>
            </ul>
        </div>
    </div>
</body>
</html>
