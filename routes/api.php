<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReasonVisitController;
use App\Http\Controllers\TypeSexController;
use App\Http\Controllers\AgeRangeController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ComponentCategoryController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\ComponentUpdateController;
use App\Http\Controllers\EventCategoryController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FilamentController;
use App\Http\Controllers\FilamentUpdateController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LaserUpdateController;
use App\Http\Controllers\MaterialLaserController;
use App\Http\Controllers\MaterialMillingController;
use App\Http\Controllers\MillingUpdateController;
use App\Http\Controllers\ObservationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResinController;
use App\Http\Controllers\ResinUpdateController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SoftwareUpdateController;
use App\Http\Controllers\StabilizerController;
use App\Http\Controllers\StabilizerUpdateController;
use App\Http\Controllers\TechExpenseController;
use App\Http\Controllers\ThreadController;
use App\Http\Controllers\ThreadUpdateController;
use App\Http\Controllers\VinylController;
use App\Http\Controllers\VinylUpdateController;
use App\Http\Controllers\VisitController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Se deben corregir rutas privadas y por roles */

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/logout', [AuthController::class,'logout']);
});

Route::controller(UserController::class)->group(function(){
    Route::get('/users', 'index');
    Route::get('/users/{user}', 'show');
    Route::post('/users','store');
    Route::put('/users/{user}','update');
    Route::put('/users/{user}/password','updatePassword');
    Route::delete('/users/{user}','destroy');
});

Route::controller(AuthController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/forgot-password', 'forgotPassword');
    Route::post('/reset-password', 'resetPassword');
});

Route::controller(AreaController::class)->group(function(){
    Route::get('/areas', 'index');
    Route::get('/areas/{area}', 'show');
    Route::post('/areas', 'store');
    Route::put('/areas/{area}', 'update');
    Route::delete('/areas/{area}', 'destroy');
    Route::get('/services', 'services');
});

Route::controller(RoleController::class)->group(function(){
    Route::get('/roles', 'index');
    Route::get('/roles/{role}', 'show');
    Route::post('/roles', 'store');
    Route::put('/roles/{role}', 'update');
    Route::delete('/roles/{role}', 'destroy');
});

Route::controller(ReasonVisitController::class)->group(function(){
    Route::get('/reason-visits', 'index');
    Route::get('/reason-visits/bookings', 'indexBookings');
    Route::get('/reason-visits/{type}', 'show');
});

Route::controller(CustomerController::class)->group(function(){
    Route::get('/customers', 'index');
    Route::get('/customers/download', 'template');
    Route::get('/customers/{customer}', 'show');
    Route::get('/customers/{type}/{search}/', 'search');
    Route::get('/customers/v/{type}/{search}/', 'isExist');
    Route::post('/customers', 'store');
    Route::put('/customers/{customer}', 'update');
    Route::delete('/customers/{customer}', 'destroy');
});

Route::controller(TypeSexController::class)->group(function(){
    Route::get('/type-sexes', 'index');
});

Route::controller(AgeRangeController::class)->group(function(){
    Route::get('/age-ranges', 'index');
});

Route::controller(VisitController::class)->group(function(){
    Route::get('/visits', 'index');
    Route::get('/visits/endtime-null', 'endTimeNull');
    Route::get('/visits/attend', 'attend');
    Route::get('/visits/{visit}', 'show');
    Route::get('/visits/{visit}/areas', 'showAreas');
    Route::post('/visits', 'store');
    Route::post('/visits/areas', 'storeAreas');
    Route::post('/visits/customers', 'storeCustomers');
    Route::put('/visits/{visit}', 'update');
    Route::put('/visits/{visit}/areas', 'updateAreas');

    Route::put('/visits/{visit}/customers', 'updateCustomers');
    Route::delete('/visits/{visit}', 'destroy');
    Route::get('/visits/{visit}/area/{area}', 'destroyAreas');
    Route::get('/visits/{visit}/customer/{area}', 'destroyCustomers');

    Route::post('/visits/areas/update', 'updateAllAreas');
    Route::post('/visits/areas/delete', 'destroyAllAreas');

    Route::post('/visits/customers/delete', 'destroyAllCustomers');
});

/* Inventory */

Route::controller(ComponentController::class)->group(function(){
    Route::get('/components', 'index');
    Route::get('/components/{component}', 'show');
    Route::get('/components/s/{search}/', 'search');
    Route::post('/components', 'store');
    Route::put('/components/{component}', 'update');
    Route::delete('/components/{component}', 'destroy');
});

Route::controller(ComponentCategoryController::class)->group(function(){
    Route::get('/component-categories', 'index');
    Route::get('/component-categories/{componentCategory}', 'show');
    Route::post('/component-categories', 'store');
    Route::put('/component-categories/{componentCategory}', 'update');
    Route::delete('/component-categories/{componentCategory}', 'destroy');
});

Route::controller(ComponentUpdateController::class)->group(function(){
    Route::get('/component-updates', 'index');
    Route::get('/component-updates/{componentUpdate}', 'show');
    Route::post('/component-updates', 'store');
    Route::put('/component-updates/{componentUpdate}', 'update');
    Route::delete('/component-updates/{componentUpdate}', 'destroy');
});

Route::controller(MaterialMillingController::class)->group(function(){
    Route::get('/materials-milling', 'index');
    Route::get('/materials-milling/{materialMilling}', 'show');
    Route::get('/materials-milling/s/{search}/', 'search');
    Route::post('/materials-milling', 'store');
    Route::put('/materials-milling/{materialMilling}', 'update');
    Route::delete('/materials-milling/{materialMilling}', 'destroy');
});

Route::controller(MillingUpdateController::class)->group(function(){
    Route::get('/milling-updates', 'index');
    Route::get('/milling-updates/{millingUpdate}', 'show');
    Route::post('/milling-updates', 'store');
    Route::put('/milling-updates/{millingUpdate}', 'update');
    Route::delete('/milling-updates/{millingUpdate}', 'destroy');
});

Route::controller(MaterialLaserController::class)->group(function(){
    Route::get('/materials-laser', 'index');
    Route::get('/materials-laser/{materialLaser}', 'show');
    Route::get('/materials-laser/s/{search}/', 'search');
    Route::post('/materials-laser', 'store');
    Route::put('/materials-laser/{materialLaser}', 'update');
    Route::delete('/materials-laser/{materialLaser}', 'destroy');
});

Route::controller(LaserUpdateController::class)->group(function(){
    Route::get('/laser-updates', 'index');
    Route::get('/laser-updates/{laserUpdate}', 'show');
    Route::post('/laser-updates', 'store');
    Route::put('/laser-updates/{laserUpdate}', 'update');
    Route::delete('/laser-updates/{laserUpdate}', 'destroy');
});

Route::controller(VinylController::class)->group(function(){
    Route::get('/vinyls', 'index');
    Route::get('/vinyls/{vinyl}', 'show');
    Route::get('/vinyls/s/{search}/', 'search');
    Route::post('/vinyls', 'store');
    Route::put('/vinyls/{vinyl}', 'update');
    Route::delete('/vinyls/{vinyl}', 'destroy');
});

Route::controller(VinylUpdateController::class)->group(function(){
    Route::get('/vinyls-updates', 'index');
    Route::get('/vinyls-updates/{vinylUpdate}', 'show');
    Route::post('/vinyls-updates', 'store');
    Route::put('/vinyls-updates/{vinylUpdate}', 'update');
    Route::delete('/vinyls-updates/{vinylUpdate}', 'destroy');
});

Route::controller(FilamentController::class)->group(function(){
    Route::get('/filaments', 'index');
    Route::get('/filaments/{filament}', 'show');
    Route::get('/filaments/s/{search}/', 'search');
    Route::post('/filaments', 'store');
    Route::put('/filaments/{filament}', 'update');
    Route::delete('/filaments/{filament}', 'destroy');
});

Route::controller(FilamentUpdateController::class)->group(function(){
    Route::get('/filaments-updates', 'index');
    Route::get('/filaments-updates/{filamentUpdate}', 'show');
    Route::post('/filaments-updates', 'store');
    Route::put('/filaments-updates/{filamentUpdate}', 'update');
    Route::delete('/filaments-updates/{filamentUpdate}', 'destroy');
});

Route::controller(ResinController::class)->group(function(){
    Route::get('/resins', 'index');
    Route::get('/resins/{resin}', 'show');
    Route::get('/resins/s/{search}/', 'search');
    Route::post('/resins', 'store');
    Route::put('/resins/{resin}', 'update');
    Route::delete('/resins/{resin}', 'destroy');
});

Route::controller(ResinUpdateController::class)->group(function(){
    Route::get('/resins-updates', 'index');
    Route::get('/resins-updates/{resinUpdate}', 'show');
    Route::post('/resins-updates', 'store');
    Route::put('/resins-updates/{resinUpdate}', 'update');
    Route::delete('/resins-updates/{resinUpdate}', 'destroy');
});

Route::controller(SoftwareController::class)->group(function(){
    Route::get('/softwares', 'index');
    Route::get('/softwares/{software}', 'show');
    Route::get('/softwares/s/{search}/', 'search');
    Route::post('/softwares', 'store');
    Route::put('/softwares/{software}', 'update');
    Route::delete('/softwares/{software}', 'destroy');
});

Route::controller(SoftwareUpdateController::class)->group(function(){
    Route::get('/softwares-updates', 'index');
    Route::get('/softwares-updates/{softwareUpdate}', 'show');
    Route::post('/softwares-updates', 'store');
    Route::put('/softwares-updates/{softwareUpdate}', 'update');
    Route::delete('/softwares-updates/{softwareUpdate}', 'destroy');
});

Route::controller(ThreadController::class)->group(function(){
    Route::get('/threads', 'index');
    Route::get('/threads/{thread}', 'show');
    Route::get('/threads/s/{search}/', 'search');
    Route::post('/threads', 'store');
    Route::put('/threads/{thread}', 'update');
    Route::delete('/threads/{thread}', 'destroy');
});

Route::controller(ThreadUpdateController::class)->group(function(){
    Route::get('/threads-updates', 'index');
    Route::get('/threads-updates/{threadUpdate}', 'show');
    Route::post('/threads-updates', 'store');
    Route::put('/threads-updates/{threadUpdate}', 'update');
    Route::delete('/threads-updates/{threadUpdate}', 'destroy');
});

Route::controller(StabilizerController::class)->group(function(){
    Route::get('/stabilizers', 'index');
    Route::get('/stabilizers/{stabilizer}', 'show');
    Route::get('/stabilizers/s/{search}/', 'search');
    Route::post('/stabilizers', 'store');
    Route::put('/stabilizers/{stabilizer}', 'update');
    Route::delete('/stabilizers/{stabilizer}', 'destroy');
});

Route::controller(StabilizerUpdateController::class)->group(function(){
    Route::get('/stabilizers-updates', 'index');
    Route::get('/stabilizers-updates/{stabilizerUpdate}', 'show');
    Route::post('/stabilizers-updates', 'store');
    Route::put('/stabilizers-updates/{stabilizerUpdate}', 'update');
    Route::delete('/stabilizers-updates/{stabilizerUpdate}', 'destroy');
});

Route::controller(EventController::class)->group(function(){
    Route::get('/events', 'index');
    Route::get('/events/{event}', 'show');
    Route::get('/events/{event}/participants', 'showParticipants');
    Route::get('/events/{category}/{search}/', 'search');
    Route::post('/events', 'store');
    Route::put('/events/{event}', 'update');
    Route::delete('/events/{event}', 'destroy');
});

Route::controller(EventCategoryController::class)->group(function(){
    Route::get('/event-categories', 'index');
    Route::get('/event-categories/{eventCategory}', 'show');
    Route::post('/event-categories', 'store');
    Route::put('/event-categories/{eventCategory}', 'update');
    Route::delete('/event-categories/{eventCategory}', 'destroy');
});

Route::controller(InvoiceController::class)->group(function(){
    Route::get('/invoices', 'index');
    Route::get('/invoices/payments', 'indexPayments');
    Route::get('/invoices/{invoice}', 'show');
    Route::post('/invoices', 'store');
    Route::put('/invoices/{invoice}', 'update');
    Route::delete('/invoices/{invoice}', 'destroy');
    Route::get('/invoices/{invoice}/pdf', 'pdf');
});

Route::controller(PaymentController::class)->group(function(){
    Route::get('/payments', 'index');
    Route::get('/payments/{payment}', 'show');
    Route::post('/payments', 'store');
    Route::put('/payments/{payment}', 'update');
    Route::delete('/payments/{payment}', 'destroy');
});

Route::controller(QuotationController::class)->group(function(){
    Route::get('/quotations', 'index');
    Route::get('/quotations/{quotation}', 'show');
    Route::post('/quotations', 'store');
    Route::put('/quotations/{quotation}', 'update');
    Route::delete('/quotations/{quotation}', 'destroy');
    Route::get('/quotations/{quotation}/pdf', 'pdf');
});

/* Reports */

Route::controller(ReportController::class)->group(function(){
    Route::get('/reports', 'index');
    Route::get('/reports/{report}', 'show');
    Route::post('/reports', 'store');
    Route::put('/reports/{report}', 'update');
    Route::delete('/reports/{report}', 'destroy');
    Route::get('/reports/{report}/pdf', 'pdf');
});

/* Obs */

Route::controller(ObservationController::class)->group(function(){
    Route::get('/observations', 'index');
    Route::get('/observations/{observation}', 'show');
    Route::post('/observations', 'store');
    Route::put('/observations/{observation}', 'update');
    Route::delete('/observations/{observation}', 'destroy');
});

Route::controller(ReasonVisitController::class)->group(function(){
    Route::get('/reason-visits', 'index');
    Route::get('/reason-visits/{reason}', 'show');
    Route::post('/reason-visits', 'store');
    Route::put('/reason-visits/{reason}', 'update');
    Route::delete('/reason-visits/{reason}', 'destroy');
});

Route::controller(TechExpenseController::class)->group(function(){
    Route::get('/tech-expenses', 'index');
    Route::get('/tech-expenses/{techExpense}', 'show');
    Route::post('/tech-expenses', 'store');
    Route::put('/tech-expenses/{techExpense}', 'update');
    Route::delete('/tech-expenses/{techExpense}', 'destroy');
});

/* Bookings */

Route::controller(BookingController::class)->group(function(){
    Route::get('/bookings', 'index');
    Route::get('/bookings/{booking}', 'show');
    Route::get('/bookings/{startStr}/{endStr}/','showSchedule');
    Route::get('/bookings/s/{type}/{search}/', 'search');
    Route::get('/bookings/{booking}/customers/pdf', 'pdf');
    Route::post('/bookings', 'store');
    Route::post('/bookings/put', 'storePut');
    Route::put('/bookings/{booking}', 'update');
    Route::delete('/bookings/{booking}', 'destroy');
});