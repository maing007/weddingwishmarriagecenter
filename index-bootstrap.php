<?php
/**
 * Shared front controller (loaded from project root index.php or public/index.php).
 * APP_ROOT must be defined before this file is included.
 */
if (!defined('APP_ROOT')) {
    throw new RuntimeException('APP_ROOT is not defined.');
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once APP_ROOT . '/config/config.php';
// Ensures matri_id_display() exists if production config.php was not updated with the require inside it.
require_once APP_ROOT . '/app/helpers/matri.php';
require_once APP_ROOT . '/core/Router.php';
require_once APP_ROOT . '/core/Controller.php';
require_once APP_ROOT . '/core/Database.php';
require_once APP_ROOT . '/app/core/Migrator.php';

Migrator::run(false);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

spl_autoload_register(function ($class) {
    $class = str_replace('\\', '/', $class);

    $controllerPath = APP_ROOT . '/app/controllers/' . $class . '.php';
    $modelPath      = APP_ROOT . '/app/models/' . $class . '.php';

    if (file_exists($controllerPath)) {
        require_once $controllerPath;
    } elseif (file_exists($modelPath)) {
        require_once $modelPath;
    }
});

$router = new Router();

/**
 * ROUTES
 */

// Home (banner + register form)
$router->get('/', 'HomeController@index');
$router->get('/about', 'HomeController@About');
$router->get('/privacy', 'HomeController@privacy');
$router->get('/demograph', 'HomeController@demograph');
$router->get('/child', 'HomeController@child');
$router->get('/faq', 'HomeController@Faq');
$router->get('/register', 'HomeController@register');
$router->get('/carees', 'HomeController@carees');
$router->get('/sucess1', 'HomeController@sucess1');
$router->get('/sucess2', 'HomeController@sucess2');
$router->get('/sucess3', 'HomeController@sucess3');


$router->get('/blogs', 'AdminBlogController@blogview');
$router->get('/blog/{slug}', 'BlogController@detail');
$router->get('/blog/id', 'AdminBlogController@blogDetailById');

$router->get('/contact', 'HomeController@contact');
$router->get('/member', 'HomeController@membership');
// Contact form submit
// POST form submit for contact page
$router->post('/contact-submit', 'ContactController@submit');

$router->get('/admin/blogs', 'AdminBlogController@index');
$router->get('/admin/blog/create', 'AdminBlogController@create');
$router->post('/admin/blog/create', 'AdminBlogController@create');
$router->get('/admin/blog/delete/{id}', 'AdminBlogController@delete');
// Display the search form
$router->get('/search', 'SearchController@index');

// Handle the search form submission (POST request)
$router->post('/search', 'SearchController@search');

// Optional: handle simplified POST search request (if using handleSearchRequest)
$router->post('/search-request', 'SearchController@handleSearchRequest');

// Display individual profile details by ID
$router->get('/profile/{id}', 'SearchController@profile');


// Auth
$router->get('/login',  'AuthController@showLoginForm');
$router->post('/login', 'AuthController@login');


// Registration (yehi tumhara /register-user hai)
$router->post('/register-user', 'AuthController@register');
$router->get('/logout', 'AuthController@logout');
// Dashboard + profile after login
$router->get('/dashboard',         'DashboardController@index');
$router->get('/dashboard/profile', 'DashboardController@editProfileForm');
$router->post('/dashboard/profile', 'DashboardController@updateProfile');
$router->post('/dashboard/save-profile', 'DashboardController@saveProfile');

$router->get('/dashboard/profile-complete', 'ProfileCompletionController@create');
$router->post('/dashboard/profile-complete/save', 'ProfileCompletionController@store');
$router->get('/dashboard/user/(\d+)', 'DashboardController@viewUserProfile');

//admin routes
$router->get('/admin/login', 'AdminAuthController@loginForm');
$router->post('/admin/login', 'AdminAuthController@login');

$router->get('/admin/register', 'AdminAuthController@registerForm');
$router->post('/admin/register', 'AdminAuthController@register');

$router->get('/admin/dashboard', 'AdminDashboardController@index');

$router->get('/admin/system/database-migrations', 'AdminMigrationsController@index');
$router->post('/admin/system/database-migrations/run', 'AdminMigrationsController@runNow');

$router->get('/admin/sales-report', 'AdminDashboardController@membersreport');
$router->get('/admin/team-management', 'AdminDashboardController@team');
$router->get('/admin/user-profiles', 'AdminUsersController@viewUserProfile');

$router->post('/admin/user-profile/update', 'AdminUsersController@updateProfile');

$router->get('/admin/add-user', 'AdminUsersController@createuser');
$router->get('/admin/store-user', 'AdminUsersController@storeuser');

$router->get('/admin/user-profile/edit', 'AdminUsersController@editProfileForm');

$router->post('/admin/delete-user', 'AdminDashboardController@deleteUser');
$router->post('/admin/mark-paid', 'AdminDashboardController@markPaid');

$router->get('/admin/logout', 'AdminAuthController@logout');

$router->get('/admin/users', 'AdminUsersController@index');
$router->get('/admin/paid-to-spotlight', 'AdminUsersController@paidToSpotlight');
$router->post('/admin/paid-to-spotlight/bulk-featured', 'AdminUsersController@bulkFeaturedSpotlight');
$router->get('/admin/change-membership-plan', 'AdminUsersController@changeMembershipPlan');
$router->get('/admin/expired-members', 'AdminUsersController@expiredMembers');
$router->get('/admin/member-followup-report', 'AdminUsersController@memberFollowupReport');
$router->get('/admin/advanced-search', 'AdminUsersController@advancedSearch');
$router->post('/admin/advanced-search', 'AdminUsersController@advancedSearch');
$router->get('/admin/match-making', 'AdminUsersController@matchMaking');

$router->get('/admin/lead-generation', 'AdminLeadGenerationController@index');
$router->post('/admin/lead-generation/bulk-interest', 'AdminLeadGenerationController@bulkInterest');
$router->post('/admin/lead-generation/comment', 'AdminLeadGenerationController@comment');
$router->get('/admin/lead-generation/comments-json', 'AdminLeadGenerationController@commentsJson');
$router->get('/admin/lead-generation/add', 'AdminLeadGenerationController@add');
$router->post('/admin/lead-generation/store', 'AdminLeadGenerationController@store');
$router->get('/admin/lead-generation/edit', 'AdminLeadGenerationController@edit');
$router->post('/admin/lead-generation/update', 'AdminLeadGenerationController@update');
$router->get('/admin/lead-generation/task', 'AdminLeadGenerationController@task');
$router->get('/admin/lead-generation/report', 'AdminLeadGenerationController@report');
$router->post('/admin/lead-generation/delete', 'AdminLeadGenerationController@deleteLead');
$router->get('/admin/lead-generation/followup-report', 'AdminLeadGenerationController@followupReport');

$router->get('/admin/reports/match-making/auto-match-email/cron-history', 'AdminAutoMatchCronController@cronHistory');
$router->post('/admin/reports/match-making/auto-match-email/cron-history/delete', 'AdminAutoMatchCronController@cronHistoryDelete');
$router->get('/admin/reports/match-making/auto-match-email/auto-generated-match', 'AdminAutoMatchCronController@autoGeneratedMatch');
$router->post('/admin/reports/match-making/auto-match-email/auto-generated-match/delete', 'AdminAutoMatchCronController@autoGeneratedMatchDelete');
$router->post('/admin/reports/match-making/auto-match-email/auto-generated-match/status', 'AdminAutoMatchCronController@autoGeneratedMatchUpdateStatus');
$router->get('/admin/reports/match-making/auto-match-email/deferred-matches', 'AdminDeferredMatchesController@index');

$router->get('/admin/reports/staff-management/staff-all-activity', 'AdminStaffActivityController@allActivity');
$router->get('/admin/reports/staff-management/staff-all-activity/export', 'AdminStaffActivityController@exportAllActivity');
$router->get('/admin/reports/staff-management/staff-activity-summary', 'AdminStaffActivityController@summary');
$router->get('/admin/reports/staff-management/staff-activity-summary/export', 'AdminStaffActivityController@exportSummary');

$router->get('/admin/reports/payments/registration-fee', 'AdminMemberSaleFeesController@registrationFeeReport');
$router->get('/admin/reports/payments/rishta-fee', 'AdminMemberSaleFeesController@rishtaFeeReport');
$router->get('/admin/accounts/income/registration-fee', 'AdminMemberSaleFeesController@registrationFee');
$router->get('/admin/accounts/income/rishta-fee', 'AdminMemberSaleFeesController@rishtaFee');
$router->post('/admin/accounts/income/fee-paid-approved', 'AdminMemberSaleFeesController@feePaidApproved');
$router->post('/admin/accounts/income/assign-plan', 'AdminMemberSaleFeesController@assignPlanSubmit');
$router->post('/admin/accounts/income/payment-proof', 'AdminMemberSaleFeesController@paymentProofSubmit');
$router->get('/admin/accounts/invoice/registration', 'AdminMemberSaleFeesController@registrationInvoice');

$router->get('/admin/reports/meetings/meeting-summary', 'AdminMeetingSummaryController@index');

$router->get('/admin/reports/members/members-email-verification', 'AdminReportsMembersController@emailVerification');
$router->get('/admin/reports/members/members-summary', 'AdminReportsMembersController@membersSummary');
$router->get('/admin/reports/members/unsubscribe-member', 'AdminReportsMembersController@unsubscribeMember');
$router->get('/admin/reports/members/members-all-activity', 'AdminReportsMembersController@membersAllActivity');
$router->get('/admin/reports/members/members-all-activity/export', 'AdminReportsMembersController@membersAllActivityExport');
$router->get('/admin/auto-match-cron-history', 'AdminAutoMatchCronController@legacyCronHistoryRedirect');
$router->post('/admin/auto-match-cron-history/delete', 'AdminAutoMatchCronController@cronHistoryDelete');
$router->get('/admin/member-evaluation', 'AdminUsersController@memberEvaluationForm');
$router->post('/admin/member-evaluation', 'AdminUsersController@saveMemberEvaluationForm');
$router->get('/admin/accepted-matches', 'AdminUsersController@acceptedMatches');
$router->get('/admin/users/interactions', 'AdminUsersController@interactionReport');
$router->post('/admin/users/comment', 'AdminUsersController@addComment');
$router->get('/admin/users/comments-json', 'AdminUsersController@commentsJson');
$router->get('/admin/users/member-dynamic-team-json', 'AdminUsersController@memberDynamicTeamJson');
$router->get('/admin/users/profile-view', 'AdminUsersController@adminProfileView');
$router->get('/admin/users/profile-pdf-template', 'AdminUsersController@profilePdfTemplate');
$router->post('/admin/users/send-email-confirmation', 'AdminUsersController@sendEmailConfirmation');
$router->get('/admin/users/edit-steps', 'AdminUsersController@editProfileSteps');
$router->post('/admin/users/edit-steps', 'AdminUsersController@updateProfileSteps');
$router->get('/admin/users/open-task', 'AdminUsersController@openTaskForm');
$router->post('/admin/users/open-task', 'AdminUsersController@storeTask');
$router->post('/admin/users/bulk-status', 'AdminUsersController@bulkStatus');
$router->get('/admin/paid-profiles', 'AdminPaidProfilesController@index');
$router->get('/admin/invoices', 'AdminInvoicesController@index');
$router->get('/admin/invoices/download', 'AdminInvoicesController@download');

$router->post('/admin/user/delete', 'AdminUsersController@delete');

$router->get('/admin/assign-package', 'AdminPackagesController@assignForm');
$router->post('/admin/assign-package', 'AdminPackagesController@assign');
$router->get('/admin/paid-profiles/edit', 'AdminPaidProfilesController@edit');

$router->post('/admin/paid-profiles/update', 'AdminPaidProfilesController@update');
$router->post('/admin/paid-profiles/delete', 'AdminPaidProfilesController@delete');
$router->get('/admin/contact-messages', 'AdminDashboardController@show_messages');
// delete single
$router->get('/admin/contact/delete/{id}', 'AdminDashboardController@deleteMessage');

// bulk actions
$router->post('/admin/contact/bulk-action', 'AdminDashboardController@bulkMessagesAction');

// export all
$router->get('/admin/contact/export-all', 'AdminDashboardController@exportAllMessages');





$router->get('/admin/mail/inbox', 'AdminMailController@inbox');
$router->get('/admin/mail/compose', 'AdminMailController@compose');
$router->post('/admin/mail/send', 'AdminMailController@send');
// Admin Assign Member
$router->post('/adminusers/assignMember', 'AdminUsersController@assignMember');
$router->get('/dashboard/openAssignment', 'DashboardController@openAssignment');
$router->get('/dashboard/accept-assignment', 'DashboardController@acceptAssignment');
$router->get('/dashboard/decline-assignment', 'DashboardController@declineAssignment');

$router->post('/admin/user/basic', 'AdminUserDetailsController@saveBasicDetails');
$router->post('/admin/user/residence', 'AdminUserDetailsController@saveResidence');
$router->post('/admin/user/physical', 'AdminUserDetailsController@savePhysical');
$router->post('/admin/user/other', 'AdminUserDetailsController@saveOtherInfo');
$router->post('/admin/user/partner', 'AdminUserDetailsController@savePartner');
$router->post('/admin/user/submit', 'AdminUserDetailsController@submitAll');

/* ===============================
   USER ADD - MULTI STEP FORM (GET)
=============================== */

// 1. Basic Details Form
$router->get('/admin/add_user/user/basic', 'AdminUserDetailsController@basicForm');

// 2. Residence Form
$router->get('/admin/add_user/user/residence', 'AdminUserDetailsController@residenceForm');

// 3. Physical Info Form
$router->get('/admin/add_user/user/physical', 'AdminUserDetailsController@physicalForm');

// 4. Other Info Form
$router->get('/admin/add_user/user/other', 'AdminUserDetailsController@otherForm');

// 5. Partner Preference Form
$router->get('/admin/add_user/user/partner', 'AdminUserDetailsController@partnerForm');

// 6. Upload Form (Final Step)
$router->get('/admin/add_user/user/upload', 'AdminUserDetailsController@uploadForm');



// 🔥 NEW – full profile view
$router->get('/dashboard/profile/view',  'DashboardController@viewProfile');
$router->get('/dashboard/saved-profiles', 'DashboardController@savedProfiles');

$router->post('/admin/team-management/bulk-action', 'AdminDashboardController@bulkAction');
$router->post('/admin/team-management/attendance-data', 'AdminDashboardController@attendanceData');
$router->post('/admin/team-management/mark-attendance', 'AdminDashboardController@markAttendance');
$router->get('/admin/change-password', 'AdminDashboardController@changePasswordForm');
$router->post('/admin/update-password', 'AdminDashboardController@updatePassword');

$mailController = new MailController();

$router->get('/mail/send', 'MailController@sendTest');
// Dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
