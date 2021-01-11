<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\User;
use Auth;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware( 'auth' );
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {

        $data['user'] = \Auth::user();//retureaza userul curent

        // print_r($user->id);
        $data['player'] = User::select(['id','name'])->where('id','!=',$data['user']->id)->get();
        // print_r($player);

        return view( 'home',$data);
    }

    /**
     * Returns the user's current room
     *
     * @return mixed
     */
    public function getRoom($id) {

        $user = Auth::user();
        $room = Room::where(['id'=>$id])->first();
        return $room;
    }

    /**
     * Here the method that is called every time a user enters the page
     * If the user is not in a room we will create one for him.
     *
     * @return int
     */
    public function play($opponent) {

//              return view( 'play' );
        $user = Auth::user();

        // check for empty Rooms
        $room = Room::where(['user1_id'=>$user->id,'user2_id'=>$opponent])->first();
        // print_r($room);
        if($room){
            if($room->user2_id==$user->id){
                $tmp=Room::find($room->id);
                $tmp->accepted=1;
                $tmp->save();
            }
            return $room->id;
        }
        else{
            $room = Room::where(['user2_id'=>$user->id,'user1_id'=>$opponent])->first();
            if($room){
                if($room->user2_id==$user->id){
                    $tmp=Room::find($room->id);
                    $tmp->accepted=1;
                    $tmp->save();
                }
                return $room->id;       
            }
             $room             = new Room();
             $room->user1_id   = $user->id;
             $room->user2_id   = $opponent;
             $room->turn       = 1;
             $room->table_data = [ [ NULL, NULL, NULL ], [ NULL, NULL, NULL ], [ NULL, NULL, NULL ] ];
             $room->save();
             return $room->id;; // "Asteapta"    
        }
        // if ( $room ) {
        //     if ( $room->user2_id )  //table_data=matricea x si 0
        //         return $room->table_data; //"Inceper joc"
        //     else
        //         return 0; // "Asteapta"
        // } else {
        //     $room = Room::where( 'user2_id', $user->id )->first();

        //     if ( $room ) {
        //         return $room->table_data; //"Incepe joc"
        //     } else {
        //         $room = Room::whereNull( 'user2_id' )->first();
        //         if ( $room ) {
        //             $room->user2_id = $user->id;
        //             $room->save();

        //             return $room->table_data; //"Inceper joc"
        //         } else {
        //             $room             = new Room();
        //             $room->user1_id   = $user->id;
        //             $room->user2_id   = NULL;
        //             $room->turn       = 1;
        //             $room->table_data = [ [ NULL, NULL, NULL ], [ NULL, NULL, NULL ], [ NULL, NULL, NULL ] ];
        //             $room->save();

        //             return 0; // "Asteapta"
        //         }
        //     }
        // }

    }

    public function leaderboard(){
        $data['result']=User::select(['name','won_games','draw_games','lose_games'])->orderBy('won_games','DESC')->orderBy('draw_games','DESC')->orderBy('lose_games','ASC')->get();
        return view('leaderboard',$data);
    }

    /**
     * Show main page
     *
     */
    public function game($opponent) {
        $room_id=$this->play($opponent);
        $room = $this->getRoom($room_id);
        $user   = Auth::user();
        $first=User::select('name')->where(['id'=>$room->user1_id])->get();
        $second=User::select('name')->where(['id'=>$room->user2_id])->get();
        $data['first']=$first[0]->name;
        $data['second']=$second[0]->name;
        $data['room_id']=$room_id;
        if($user->id==$room->user1_id){
            $data['type']='X';
        }
        else{
            $data['type']='O';   
        }
        // $data['opponent']=$opponent;
        return view( 'play' ,$data);
    }

    /**
     * Method to call every time the game needs to be updated.
     *
     * @return mixed|void
     */
    public function notif(){
        $result = array();
        $user   = Auth::user();
        array_push($result,Room::where(['user1_id'=>$user->id])->get());
        array_push($result,Room::where(['user2_id'=>$user->id])->get());
        return $result;
    }

    public function update($id) {

        $room = $this->getRoom($id);
        if( is_null($room) )
            return;

        // check who wins
        if ( $room->isFinished() ) {
            $room['winner'] = $this->winner($id);
        }

        return $room;
    }

    /**
     * Method to call every time a user adds a sign on the grid
     * Also here we check if the user wins the gameor not.
     *
     * @param Request $request
     *
     * @return array|int|void
     */
    public function addSign( Request $request ) {

        $row    = $request->get( 'row' );
        $column = $request->get( 'column' );
        $user   = Auth::user();

        // check for empty Rooms
        $room = $this->getRoom($request->id);
        if ( $room->turn == -1 ) //-1 = end of the game
            return 0;

        $tableData = $room->table_data;

        if ( !is_null( $tableData[ $row ][ $column ] ) )
            return;

        // stabilire semn jucator
        $sign = NULL;

        if ( $room->user1_id == $user->id && $room->turn == 1 ) {
            $sign       = 'X';
            $room->turn = 2;
        } elseif ( $room->user2_id == $user->id && $room->turn == 2 ) {
            $sign       = 'O';
            $room->turn = 1;
        }

        $tableData[ $row ][ $column ] = $sign;

        $room->table_data = $tableData;
        $room->save();

        $winner = $this->winner($request->id);
        if ( $winner == 1 ) {
            $user->won_games++;
            $user->save();
            $tmp=User::find($room->user2_id );
            $tmp->lose_games++;
            $tmp->save();
        }
        elseif ($winner == -1) {
            $tmp=User::find($room->user1_id );
            $tmp->draw_games++;
            $tmp->save();
            $tmp=User::find($room->user2_id );
            $tmp->draw_games++;
            $tmp->save();
        }

        if ( $winner == 1 || $winner == -1 ) {
            $room->turn = -1;
            $room->save();
        }

        // RETURN NEW DATA TO THE BROWSER
        return [
            "sign"   => $sign,
            "winner" => $winner,
        ];
    }

    /**
     * Get the game's winner
     *
     * @return int
     */
    public function winner($id) {
        $room = $this->getRoom($id);

        $user = Auth::user();
        $data = $room->table_data;

        $sign = 'O';
        if ( $room->user1_id == $user->id )
            $sign = 'X';

        return $this->checkGame( $data, $sign,$id );
    }

    private function checkGame( $data, $sign ,$id) {

        $it    = new RecursiveIteratorIterator( new RecursiveArrayIterator( $data ) );
        $board = [ ];

        $fullBoard = 0;
        foreach ( $it as $v ) {
            if ( !is_null( $v ) ) {
                $fullBoard++;
            }
            array_push( $board, $v );
        }

        $winRules = [
            [ 0, 1, 2 ],
            [ 3, 4, 5 ],
            [ 6, 7, 8 ],

            [ 0, 3, 6 ],
            [ 1, 4, 7 ],
            [ 2, 5, 8 ],

            [ 0, 4, 8 ],
            [ 2, 4, 6 ]
        ];

        // [ x, nul, o, nul, 0, 0, x, x, nul ];

        foreach ( $winRules as $rule ) {
            $win = 0;
            foreach ( $rule as $index ) {
                if ( $board[ $index ] == $sign )
                    $win++;
                if ( $win == count( $rule ) ) {
                    return 1;
                }
            }
        }

        $room = $this->getRoom($id);
        if ( $room->isFinished() )
            $room->delete();

        if ( $fullBoard == 9 )
            return -1;

        return 0;

    }
}
