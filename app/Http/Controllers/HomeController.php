<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\User;
use Auth;
use RecursiveArrayIterator;
use RecursiveIteratorIterator;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{

    public function __construct() {
        $this->middleware( 'auth' );
    }

    public function index() {

        $data['user'] = \Auth::user();
        $data['player'] = User::select(['id','name','last_login_at','logged_in'])->where('id','!=',$data['user']->id)->orderBy('last_login_at','DESC')->get();
        return view( 'home',$data);
    }

    public function getRoom($id) {

        $user = Auth::user();
        $room = Room::where(['id'=>$id])->first();
        return $room;
    }

    public function play($opponent,$rand_val=1) {
        $user = Auth::user();

        $room = Room::where(['user1_id'=>$user->id,'user2_id'=>$opponent])->first();
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
             $room->turn       = $rand_val;
             $room->table_data = [ [ NULL, NULL, NULL ], [ NULL, NULL, NULL ], [ NULL, NULL, NULL ] ];
             $room->save();
             return $room->id;
        }

    }

    public function leaderboard(){
        $data['result']=User::select(['id','name','won_games','draw_games','lose_games'])->orderBy('won_games','DESC')->orderBy('draw_games','DESC')->orderBy('lose_games','ASC')->get();
        return view('leaderboard',$data);
    }

    public function game($opponent) {
        $rand_val=rand(1,2);
        $room_id=$this->play($opponent,$rand_val);
        $room = $this->getRoom($room_id);
        $user   = Auth::user();
        $first=User::select('name')->where(['id'=>$room->user1_id])->get();
        $second=User::select('name')->where(['id'=>$room->user2_id])->get();
        $data['first']=$first[0]->name;
        $data['second']=$second[0]->name;
        $data['room_id']=$room_id;
        if($rand_val==1){
            if($user->id==$room->user1_id){
                $data['type']='X';
            }
            else{
                $data['type']='O';   
            }
        }
        else{
            if($user->id==$room->user1_id){
                $data['type']='X';
            }
            else{
                $data['type']='O';   
            }
        }   
        return view( 'play' ,$data);
    }

    public function notif(){
        $result = array();
        $user   = Auth::user();
        $tmp=User::select(['id','name','last_login_at','logged_in'])->where('id','!=',$user->id)->get();
        $online=array();
        $offline=array();
        foreach ($tmp as $key => $row) {
            $last_login=strtotime($row->last_login_at);
            if(time()-$last_login>env('SESSION_LIFETIME')*60 || $row->logged_in==0){
                array_push($offline,$row->id);
            }
            else{
                array_push($online,$row->id);
            }
        }
        // print_r($online);
        array_push($result,Room::whereIn('user2_id',$online)->where(['user1_id'=>$user->id])->get());
        array_push($result,Room::whereIn('user1_id',$online)->where(['user2_id'=>$user->id])->get());
        array_push($result,$offline);
        array_push($result,$online);
        return $result;
    }

    public function update($id) {

        $room = $this->getRoom($id);
        if( is_null($room) )
            return 3;

        if ( $room->isFinished() ) {
            $room['winner'] = $this->winner($id);
        }

        return $room;
    }

    public function edit(){
        $data['user']   = Auth::user();
        return view('edit',$data);
    }

    public function profile($id){
        $data['user']=User::select(['name','photo','won_games','draw_games','lose_games'])->where('id',$id)->first();
        return view('profile',$data);
    }

    public function updateProfile(Request $request)
    {
        $user=Auth::user();
        $tmp=User::find($user->id);
        $request->validate([
            'photo' => 'image|mimes:jpeg,png,jpg,svg|max:2048|dimensions:min_width=100,min_height=200,max_width=1024,max_height=1024',
            'email' => 'unique:users,email,'.$user->id,
        ]);
        if($request->oldpass!='' && $request->newpass!=''){
            $messages = [
            'newpass.regex' => 'Password must containt uppercase,lowercase,number,and special characters!'
            ];
            $request->validate([
                'newpass' => 'min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/|different:oldpass'
            ],$messages);
            if (Hash::check($request->oldpass, $user->password)){
                $tmp->password= Hash::make($request->newpass);
            }
            else{
                return back()->with('message', "Your Password doesn't match");
            }
        }
        if($request->hasFile('photo')){
            $imageName = time().'.'.$request->photo->getClientOriginalExtension();;
            $request->photo->move(public_path('images/profile'), $imageName);
            $tmp->photo=$imageName;
            $tmp->name=$request->name;
            $tmp->email=$request->email;
            $tmp->save();
        }
        else{
            $tmp->name=$request->name;
            $tmp->email=$request->email;
            $tmp->save();   
        }
        return back()->with('success', "Success Update Profile");
    }
    
    public function decline($id){
        $user=Auth::user();
        Room::where(['user2_id'=>$user->id,'user1_id'=>$id])->delete();
        Room::where(['user1_id'=>$user->id,'user2_id'=>$id])->delete();
        return true;
    }

    public function addSign( Request $request ) {

        $row    = $request->get( 'row' );
        $column = $request->get( 'column' );
        $user   = Auth::user();

        $room = $this->getRoom($request->id);
        if ( $room->turn == -1 )
            return 0;

        $tableData = $room->table_data;

        if ( !is_null( $tableData[ $row ][ $column ] ) )
            return;

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
            if($room->user1_id==$user->id){
                $tmp=User::find($room->user2_id);
            }
            else{
                $tmp=User::find($room->user1_id);   
            }
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

        return [
            "sign"   => $sign,
            "winner" => $winner,
        ];
    }

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
