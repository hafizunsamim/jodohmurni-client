<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Models\User;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\PreferenceController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SwipeController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\ToyyibpayWebhookController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\EarlyBirdAdminController;
use App\Http\Controllers\AffiliateController;
use App\Http\Controllers\PublicAffiliateController;
use App\Http\Controllers\ExternalAffiliateAuthController;
use App\Http\Controllers\ExternalAffiliateDashboardController;
use App\Http\Controllers\ExternalAffiliatePasswordController;

/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

// set negara dari modal landing (AJAX)
Route::post('/landing/set-country', [LandingController::class, 'setCountry'])->name('landing.setCountry');
Route::post('/set-country', [LandingController::class, 'setCountry'])->name('set.country');

// Toyyibpay webhook (callback) – tanpa auth, CSRF excluded
Route::post('/subscription/toyyibpay/callback', ToyyibpayWebhookController::class)->name('subscription.toyyibpay.callback');

//PWA
Route::view('/offline', 'offline');

/*
|--------------------------------------------------------------------------
| Onboarding
|--------------------------------------------------------------------------
*/
Route::get('/onboarding/country', [OnboardingController::class, 'country'])->name('onboarding.country');
Route::post('/onboarding/country', [OnboardingController::class, 'storeCountry']);

Route::get('/onboarding/state', [OnboardingController::class, 'state'])->name('onboarding.state');
Route::post('/onboarding/state', [OnboardingController::class, 'storeState']);

Route::get('/onboarding/district', [OnboardingController::class, 'district'])->name('onboarding.district');
Route::post('/onboarding/district', [OnboardingController::class, 'storeDistrict']);

Route::get('/onboarding/gender', [OnboardingController::class, 'gender'])->name('onboarding.gender');
Route::post('/onboarding/gender', [OnboardingController::class, 'storeGender']);

// LELAKI
Route::get('/onboarding/male/status', [OnboardingController::class, 'maleStatus'])->name('onboarding.male.status');
Route::post('/onboarding/male/status', [OnboardingController::class, 'storeMaleStatus']);

Route::get('/onboarding/male/monogamy-info', [OnboardingController::class, 'maleMonogamyInfo'])->name('onboarding.male.monogamy_info');

Route::get('/onboarding/male/polygamy-confirm', [OnboardingController::class, 'malePolygamyConfirm'])->name('onboarding.male.polygamy_confirm');
Route::post('/onboarding/male/polygamy-confirm', [OnboardingController::class, 'storeMalePolygamyConfirm']);

Route::get('/onboarding/male/polygamy-situation', [OnboardingController::class, 'malePolygamySituation'])->name('onboarding.male.polygamy_situation');
Route::post('/onboarding/male/polygamy-situation', [OnboardingController::class, 'storeMalePolygamySituation']);

Route::get('/onboarding/male/polygamy-summary', [OnboardingController::class, 'malePolygamySummary'])->name('onboarding.male.polygamy_summary');

// PEREMPUAN
Route::get('/onboarding/female/preference', [OnboardingController::class, 'femalePreference'])->name('onboarding.female.preference');
Route::post('/onboarding/female/preference', [OnboardingController::class, 'storeFemalePreference']);

Route::get('/onboarding/female/monogamy-info', [OnboardingController::class, 'femaleMonogamyInfo'])->name('onboarding.female.monogamy_info');

Route::get('/onboarding/female/polygamy-level', [OnboardingController::class, 'femalePolygamyLevel'])->name('onboarding.female.polygamy_level');
Route::post('/onboarding/female/polygamy-level', [OnboardingController::class, 'storeFemalePolygamyLevel']);

Route::get('/onboarding/female/polygamy-summary', [OnboardingController::class, 'femalePolygamySummary'])->name('onboarding.female.polygamy_summary');

// Upload gambar
Route::get('/onboarding/photos', [OnboardingController::class, 'photos'])->name('onboarding.photos');
Route::post('/onboarding/photos', [OnboardingController::class, 'storePhotos'])->name('onboarding.photos.store');

// Maklumat peribadi
Route::get('/onboarding/personal-info', [OnboardingController::class, 'personalInfo'])->name('onboarding.personal_info');
Route::post('/onboarding/personal-info', [OnboardingController::class, 'storePersonalInfo']);

Route::post('/onboarding/hobbies/add', [OnboardingController::class, 'storeNewHobby'])->name('onboarding.hobbies.add');
Route::post('/onboarding/social-activities/add', [OnboardingController::class, 'storeNewSocialActivity'])->name('onboarding.social_activities.add');

// Ciri calon idaman
Route::get('/onboarding/matching-preferences', [OnboardingController::class, 'matchingPreferences'])->name('onboarding.matching_preferences');
Route::post('/onboarding/matching-preferences', [OnboardingController::class, 'storeMatchingPreferences']);

// TNC
Route::view('/terms', 'legal.terms')->name('terms');
Route::view('/privacy', 'legal.privacy')->name('privacy');

// Keahlian (perbandingan LITE / ACTIVE / GRADUATE / HYPE) – boleh akses tanpa login
Route::get('/keahlian', [MembershipController::class, 'info'])->name('keahlian.info');

/*
|--------------------------------------------------------------------------
| Public Affiliate Application (no login)
|--------------------------------------------------------------------------
*/
Route::get('/affiliate', [PublicAffiliateController::class, 'show'])->name('affiliate.public');
Route::post('/affiliate/apply', [PublicAffiliateController::class, 'apply'])->name('affiliate.public.apply');
Route::post('/affiliate/external-login', [ExternalAffiliateAuthController::class, 'login'])->name('affiliate.external.login');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (require login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Real-time: auth untuk private channel (Echo subscribe conversation.* / user.*)
    Broadcast::routes(['middleware' => ['web', 'auth']]);

    Route::get('/membership/education', [MembershipController::class, 'education'])->name('membership.education');
    Route::get('/membership/acknowledge-lite', [MembershipController::class, 'acknowledgeLite'])->name('membership.acknowledge-lite');
    Route::get('/membership/acknowledge-upgrade', [MembershipController::class, 'acknowledgeUpgrade'])->name('membership.acknowledge-upgrade');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ✅ IMPORTANT: route name ini yang Blade guna
    Route::post('/me/location', [DashboardController::class, 'updateLocation'])
        ->name('me.location.update');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/candidates/{user}', [CandidateController::class, 'show'])->name('candidates.show');

    Route::get('/preferences', [PreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences', [PreferenceController::class, 'update'])->name('preferences.update');

    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/user/{user}', [ChatController::class, 'openWithUser'])->name('chat.openWithUser');
    Route::get('/chat/{conversation:uuid}', [ChatController::class, 'showRoom'])->name('chat.room.show');
    Route::post('/chat/{conversation:uuid}', [ChatController::class, 'sendToRoom'])->name('chat.room.send');
    Route::post('/chat/{conversation:uuid}/read', [ChatController::class, 'markAsRead'])->name('chat.room.read');

    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    Route::post('/swipe', [SwipeController::class, 'store'])->name('swipe.store');
    Route::get('/likes', [SwipeController::class, 'index'])->name('swipes.index');

    // Admin: Early Bird (pakej HYPE)
    Route::get('/admin/earlybird', [EarlyBirdAdminController::class, 'index'])->name('admin.earlybird.index');
    Route::post('/admin/earlybird/toggle', [EarlyBirdAdminController::class, 'toggle'])->name('admin.earlybird.toggle');

    // subscription
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('/subscription/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscription.subscribe');
    Route::post('/subscription/paypal/create-order', [SubscriptionController::class, 'createPayPalOrder'])->name('subscription.paypal.create');
    Route::get('/subscription/paypal/return', [SubscriptionController::class, 'paypalReturn'])->name('subscription.paypal.return');
    Route::get('/subscription/paypal/cancel', [SubscriptionController::class, 'paypalCancel'])->name('subscription.paypal.cancel');
    Route::post('/subscription/toyyibpay/create-bill', [SubscriptionController::class, 'createToyyibpayBill'])->name('subscription.toyyibpay.create');
    Route::get('/subscription/toyyibpay/return', [SubscriptionController::class, 'toyyibpayReturn'])->name('subscription.toyyibpay.return');
    Route::get('/subscription/ebook-links', [SubscriptionController::class, 'ebookLinks'])->name('subscription.ebook.links');

    // optional: kekalkan route lama /subscribe (kalau ada code lama)
    Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe');

    // ebook download
    Route::get('/ebook/download', [SubscriptionController::class, 'downloadEbook'])->name('ebook.download');

    // affiliate pro request (ACTIVE/GRADUATE sahaja)
    Route::post('/affiliate/pro-request', [AffiliateController::class, 'submitProRequest'])->name('affiliate.pro.request');

    // external affiliate dashboard
    Route::get('/affiliate/dashboard', [ExternalAffiliateDashboardController::class, 'index'])->name('affiliate.external.dashboard');
    Route::post('/affiliate/dashboard/password', [ExternalAffiliatePasswordController::class, 'update'])->name('affiliate.external.password.update');
});

/*
|--------------------------------------------------------------------------
| Development Route (optional)
|--------------------------------------------------------------------------
*/
Route::get('/dev/fill-user-uuid', function () {
    User::whereNull('uuid')->chunkById(100, function ($users) {
        foreach ($users as $user) {
            $user->uuid = (string) Str::uuid();
            $user->save();
        }
    });
    return 'DONE: uuid diisi untuk semua users.';
});

// Debug helper (local only): verify runtime PHP version
if (app()->environment('local')) {
    Route::get('/__debug/php', function () {
        return response()->json([
            'php_version' => PHP_VERSION,
            'app_env' => app()->environment(),
        ]);
    });
}
