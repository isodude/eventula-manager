<?php

namespace App\Http\Controllers\Admin;

use DB;
use Auth;
use Settings;
use Colors;
use Helpers;
use FacebookPageWrapper as Facebook;
use \Carbon\Carbon as Carbon;

use App\User;
use App\Event;
use App\ShopOrder;
use App\Poll;
use App\PollOptionVote;
use App\EventParticipant;
use App\EventTournament;
use App\NewsComment;
use App\EventTicket;
use App\EventTournamentParticipant;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Show Admin Index Page
     * @return view
     */
    public function index()
    {
        $user = Auth::user();
        $users = User::all();
        $events = Event::all();
        $orders = ShopOrder::getNewOrders('login');
        $participants = EventParticipant::getNewParticipants('login');
        $participantCount = EventParticipant::all()->count();
        $tournamentCount = EventTournament::all()->count();
        $tournamentParticipantCount = EventTournamentParticipant::all()->count();
        $votes = PollOptionVote::getNewVotes('login');
        $comments = NewsComment::getNewComments('login');
        $tickets = EventTicket::all();
        $activePolls = Poll::where('end', '==', null)->orWhereBetween('end', ['0000-00-00 00:00:00', date("Y-m-d H:i:s")]);
        $facebookCallback = null;
        if (Facebook::isEnabled() && !Facebook::isLinked()) {
            $facebookCallback = Facebook::getLoginUrl();
        }
        $userLastLoggedIn = User::where('id', '!=', Auth::id())->latest('last_login')->first();
        $loginSupportedGateways = Settings::getSupportedLoginMethods();
        foreach ($loginSupportedGateways as $gateway) {
            $count = 0;
            switch ($gateway) {
                case 'steam':
                    $count = $users->where('steamid', '!=', null)->count();
                    break;
                default:
                    $count = $users->where('password', '!=', null)->count();
                    break;
            }
            $userLoginMethodCount[$gateway] = $count;
        }
        return view('admin.index')
            ->withUser($user)
            ->withEvents($events)
            ->withOrders($orders)
            ->withParticipants($participants)
            ->withVotes($votes)
            ->withComments($comments)
            ->withTickets($tickets)
            ->withActivePolls($activePolls)
            ->withShopEnabled(Settings::isShopEnabled())
            ->withGalleryEnabled(Settings::isGalleryEnabled())
            ->withHelpEnabled(Settings::isHelpEnabled())
            ->withCreditEnabled(Settings::isCreditEnabled())
            ->withSupportedLoginMethods(Settings::getSupportedLoginMethods())
            ->withActiveLoginMethods(Settings::getLoginMethods())
            ->withSupportedPaymentGateways(Settings::getSupportedPaymentGateways())
            ->withActivePaymentGateways(Settings::getPaymentGateways())
            ->withFacebookCallback($facebookCallback)
            ->withUserLastLoggedIn($userLastLoggedIn)
            ->withUserCount($users->count())
            ->withUserLoginMethodCount($userLoginMethodCount)
            ->withParticipantCount($participantCount)
            ->withNextEvent(Helpers::getNextEventName())
            ->withTournamentCount($tournamentCount)
            ->withTournamentParticipantCount($tournamentParticipantCount)
            ->withOrderBreakdown(
                collect(range(1, 12))
                ->mapWithKeys(function ($month) {
                    return [Carbon::now()->startOfYear()->addMonthsNoOverflow($month - 1)->format('F') => 0];
                })
                ->merge(
                    ShopOrder::where('created_at', '>=', Carbon::now()->subMonths(12))
                        ->get()
                        ->groupBy(function ($order) {
                            return Carbon::parse($order->created_at)->format('F');
                        })
                        ->map->count()
                )
                ->all()
            )
            ->withTicketBreakdown(
                collect(range(0, 11))
                ->mapWithKeys(function ($month) {
                    return [Carbon::now()->startOfYear()->addMonthsNoOverflow($month)->format('F') => 0];
                })
                ->merge(
                    EventParticipant::where('created_at', '>=', Carbon::now()->subMonths(12))
                        ->get()
                        ->groupBy(function ($participant) {
                            return Carbon::parse($participant->created_at)->format('F');
                        })
                        ->map->count()
                )
                ->all()   
        );
    }
}
