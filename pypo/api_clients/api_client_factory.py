import airtime_api_client
import obp_api_client

def create_api_client(config):
    if config["api_client"] == "airtime":	
        return campcaster_api_client.AirtimeApiClient(config)
    elif config["api_client"] == "obp":
        return obp_api_client.ObpApiClient(config)
		
