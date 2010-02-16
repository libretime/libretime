<?php
///////////////////////////////////////////
//
// twitterPHP
// version 0.1
// By David Billingham
// david [at] slawcup [dot] com
// http://twitter.slawcup.com/twitter.class.phps
//
//
// Example 1:
//
// $t= new twitter();
// $res = $t->publicTimeline();
// if($res===false){
//   echo "ERROR<hr/>";
//     echo "<pre>";
//   print_r($t->responseInfo);
//     echo "</pre>";
// }else{
//   echo "SUCCESS<hr/>";
//     echo "<pre>";
//   print_r($res);
//     echo "</pre>";
// }
//
//
// Example 2:
//
// $t= new twitter();
// $t->username='username';
// $t->password='password';
// $res = $t->update('i am testing twitter.class.php');
// if($res===false){
//   echo "ERROR<hr/>";
//     echo "<pre>";
//   print_r($t->responseInfo);
//     echo "</pre>";
// }else{
//   echo "SUCCESS<hr/>Status Posted";
// }
//
//
//////////////////////////////////////////

class twitter{
    var $username='';
    var $password='';
    var $user_agent='';
    
    ///////////////
    //
    // I don't know if these headers have become standards yet
    // but I would suggest using them.
    // more discussion here.
    // http://tinyurl.com/3xtx66
    //
    ///////////////
    var $headers=array('X-Twitter-Client: ',
                                            'X-Twitter-Client-Version: ',
                                            'X-Twitter-Client-URL: ');
    
    var $responseInfo=array();
                                            
    
    function twitter(){}
    
    
    
    
    
    /////////////////////////////////////////
    //
    // Twitter API calls
    //
    // $this->update($status)
    // $this->publicTimeline($sinceid=false)
    // $this->friendsTimeline($id=false,$since=false)
    // $this->userTimeline($id=false,$count=20,$since=false)
    // $this->showStatus($id)
    // $this->friends($id=false)
    // $this->followers()
    // $this->featured()
    // $this->showUser($id)
    // $this->directMessages($since=false)
    // $this->sendDirectMessage($user,$text)
    //
    // If SimpleXMLElement exists the results will be returned as a SimpleXMLElement
    // otherwise the raw XML will be returned for a successful request.  If the request
    // fails a FALSE will be returned.
    //
    //
    /////////////////////////////////////////
    
    
    // Updates the authenticating user's status.  
    // Requires the status parameter specified below.
    //
    // status. (string) Required.  The text of your status update.  Must not be
    //                             more than 160 characters and should not be
    //                             more than 140 characters to ensure optimal display.
    //
    function update($status){
        $request = 'http://twitter.com/statuses/update.xml';
        $postargs = 'status='.urlencode($status);
        return $this->process($request,$postargs);
    }
    
    // Returns the 20 most recent statuses from non-protected users who have
    // set a custom user icon.  Does not require authentication.
    //
    // sinceid. (int) Optional.  Returns only public statuses with an ID greater
    //                           than (that is, more recent than) the specified ID.
    //
    function publicTimeline($sinceid=false){
        $qs='';
        if($sinceid!==false)
            $qs='?since_id='.intval($sinceid);
        $request = 'http://twitter.com/statuses/public_timeline.xml'.$qs;
        return $this->process($request);
    }
    
    // Returns the 20 most recent statuses posted in the last 24 hours from the
    // authenticating user and that user's friends.  It's also possible to request
    // another user's friends_timeline via the id parameter below.
    //
    // id. (string OR int) Optional.  Specifies the ID or screen name of the user for whom
    //                                to return the friends_timeline. (set to false if you
    //                                want to use authenticated user).
    // since. (HTTP-formatted date) Optional.  Narrows the returned results to just those
    //                                         statuses created after the specified date.  
    //
    function friendsTimeline($id=false,$since=false){
        $qs='';
        if($since!==false)
            $qs='?since='.urlencode($since);
            
        if($id===false)
            $request = 'http://twitter.com/statuses/friends_timeline.xml'.$qs;
        else
            $request = 'http://twitter.com/statuses/friends_timeline/'.urlencode($id).'.xml'.$qs;
        
        return $this->process($request);
    }
    
    // Returns the 20 most recent statuses posted in the last 24 hours from the
    // authenticating user.  It's also possible to request another user's timeline
    // via the id parameter below.
    //
    // id. (string OR int) Optional.  Specifies the ID or screen name of the user for whom
    //                                to return the user_timeline.
    // count. (int) Optional.  Specifies the number of statuses to retrieve.  May not be
    //                         greater than 20 for performance purposes.
    // since. (HTTP-formatted date) Optional.  Narrows the returned results to just those
    //                                         statuses created after the specified date.
    //
    function userTimeline($id=false,$count=20,$since=false){
        $qs='?count='.intval($count);
        if($since!==false)
            $qs .= '&since='.urlencode($since);
            
        if($id===false)
            $request = 'http://twitter.com/statuses/user_timeline.xml'.$qs;
        else
            $request = 'http://twitter.com/statuses/user_timeline/'.urlencode($id).'.xml'.$qs;
        
        return $this->process($request);
    }
    
    // Returns a single status, specified by the id parameter below.  The status's author
    // will be returned inline.
    //
    // id. (int) Required.  Returns status of the specified ID.
    //
    function showStatus($id){
        $request = 'http://twitter.com/statuses/show/'.intval($id).'.xml';
        return $this->process($request);
    }
    // Returns the authenticating user's friends, each with current status inline.  It's
    // also possible to request another user's friends list via the id parameter below.
    //
    // id. (string OR int) Optional.  The ID or screen name of the user for whom to request
    //                                a list of friends.
    //
    function friends($id=false){
        if($id===false)
            $request = 'http://twitter.com/statuses/friends.xml';
        else
            $request = 'http://twitter.com/statuses/friends/'.urlencode($id).'.xml';
        return $this->process($request);
    }
    
    // Returns the authenticating user's followers, each with current status inline.
    //
    function followers(){
        $request = 'http://twitter.com/statuses/followers.xml';
        return $this->process($request);
    }
    
    // Returns a list of the users currently featured on the site with their current statuses inline.
    function featured(){
        $request = 'http://twitter.com/statuses/featured.xml';
        return $this->process($request);
    }
    
    // Returns extended information of a given user, specified by ID or screen name as per the required
    // id parameter below.  This information includes design settings, so third party developers can theme
    // their widgets according to a given user's preferences.
    //
    // id. (string OR int) Required.  The ID or screen name of a user.
    //
    function showUser($id){
        $request = 'http://twitter.com/users/show/'.urlencode($id).'.xml';
        return $this->process($request);
    }
    
    // Returns a list of the direct messages sent to the authenticating user.
    //
    // since. (HTTP-formatted date) Optional.  Narrows the resulting list of direct messages to just those
    //                                         sent after the specified date.  
    //
    function directMessages($since=false){
        $qs='';
        if($since!==false)
            $qs='?since='.urlencode($since);
        $request = 'http://twitter.com/direct_messages.xml'.$qs;
        return $this->process($request);
    }
    
    // Sends a new direct message to the specified user from the authenticating user.  Requires both the user
    // and text parameters below.
    //
    // user. (string OR int) Required.  The ID or screen name of the recipient user.
    // text. (string) Required.  The text of your direct message.  Be sure to URL encode as necessary, and keep
    //                           it under 140 characters.  
    //
    function sendDirectMessage($user,$text){
        $request = 'http://twitter.com/direct_messages/new.xml';
        $postargs = 'user='.urlencode($user).'&text='.urlencode($text);
        return $this->process($request,$postargs);
    }
    
    
    
    
    
    // internal function where all the juicy curl fun takes place
    // this should not be called by anything external unless you are
    // doing something else completely then knock youself out.
    function process($url,$postargs=false){
        
        $ch = curl_init($url);

        if($postargs !== false){
            curl_setopt ($ch, CURLOPT_POST, true);
            curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
        }
        
        if($this->username !== false && $this->password !== false)
            curl_setopt($ch, CURLOPT_USERPWD, $this->username.':'.$this->password);
        
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_NOBODY, 0);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);

        $response = curl_exec($ch);
        
        $this->responseInfo=curl_getinfo($ch);
        curl_close($ch);
        
        
        if(intval($this->responseInfo['http_code'])==200){
            if(class_exists('SimpleXMLElement')){
                $xml = new SimpleXMLElement($response);
                return $xml;
            }else{
                return $response;    
            }
        }else{
            return false;
        }
    }
}
