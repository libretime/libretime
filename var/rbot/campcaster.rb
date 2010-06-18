# Plugin for the Ruby IRC bot (http://linuxbrit.co.uk/rbot/)
#
# Looks up information for Trac tickets for Campcaster.
#
# (c) 2006 Mark Kretschmann <markey@web.de>
# Licensed under GPL V2.

require 'cgi'
begin
  require 'rubyful_soup'
rescue
  warning "could not load rubyful_soup, urban dictionary disabled"
  warning "please get it from http://www.crummy.com/software/RubyfulSoup/"
  warning "or install it via gem"
  return
end
require 'uri/common'


class CampcasterPlugin < Plugin

  def help( plugin, topic="")
    "cc <number> => Look up information for the Campcaster ticket <number>."
  end

  def handle_ticket( m, params )
    ticket = params[:ticket]
    if ticket.to_i > 0
      url = "http://trac.campware.org/campcaster/ticket/#{ticket}"
      uri = URI.parse( url )
    else
      m.reply "Usage: #{help nil}"
      return
    end

    soup = BeautifulSoup.new( @bot.httputil.get_cached( uri ) )
    if summary = soup.find( 'h2', :attrs => { 'class' => 'summary' } )
      status = soup.find( 'h3', :attrs => { 'class' => 'status' } ).strong
      m.reply "TICKET: #{ticket} | SUMMARY: #{summary.contents} | STATUS: #{status.contents} | URL: #{url}"
    else
      m.reply "Ticket #{ticket} not found."
    end
  end

  def handle_ls( m, params )
    m.reply help( nil, nil )
  end

end


plugin = CampcasterPlugin.new
plugin.register( "cc" )

plugin.map 'cc :ticket', :action => 'handle_ticket'
plugin.map 'ls :ticket', :action => 'handle_ls'

