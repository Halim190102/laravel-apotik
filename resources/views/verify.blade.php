<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Email Verification Status</title>
        <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
    </head>

    <body>
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2>Email Verification Status</h2>
                </div>
                <div class="card-body">
                    @if ($message === 'success')
                        <div class="status success">
                            <h1>Congratulations!</h1>
                            <p>Your email has been successfully verified.</p>
                        </div>
                    @elseif ($message === 'token_expired')
                        <div class="status expired">
                            <h1>Token Expired</h1>
                            <p>Your verification token has expired. Please resend the verification email.</p>
                            <form id="resendForm" method="POST" action="{{ route('resendEmailVerificationLink') }}">
                                @csrf
                                <input type="hidden" name="email" value="{{ $email }}">
                                <button type="submit" class="btn">Resend Verification Email</button>
                            </form>
                            <div id="alertMessage" class="alert" style="display: none;">Verification email resent.
                                Please check your inbox.</div>
                        </div>
                    @elseif ($message === 'already_verified')
                        <div class="status already-verified">
                            <h1>Email Already Verified</h1>
                            <p>Your email has already been verified. Thank you!</p>
                        </div>
                    @elseif ($message === 'invalid')
                        <div class="status invalid">
                            <h1>Invalid Token</h1>
                            <p>The token provided is invalid. Please check the link in your email and try again.</p>
                        </div>
                    @else
                        <div class="status error">
                            <h1>Error</h1>
                            <p>An error occurred. Please try again later.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <script src="{{ asset('js/verify.js') }}"></script>
    </body>

</html>
