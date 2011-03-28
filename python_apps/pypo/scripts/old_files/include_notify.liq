########################################
# call pypo api gateway
########################################

def notify(m)

  if !playlist_type=='5' then 
    #print('livesession')
    system("./notify.sh --data='#{!pypo_data}' --media-id=#{m['media_id']} --export-source=scheduler")
  end
   
  if !playlist_type=='6' then
    #print('couchcaster')
    system("./notify.sh --data='#{!pypo_data}' --media-id=#{m['media_id']} --export-source=scheduler")
  end 
    
  if !playlist_type=='0' or !playlist_type=='1' or !playlist_type=='2' or !playlist_type=='3' or !playlist_type=='4' then
    #print('include_notify.liq: notify on playlist')
    system("./notify.sh --data='#{!pypo_data}' --media-id=#{m['media_id']}")
  end
  
end