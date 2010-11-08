import campcaster_api_client
import obp_api_client

def create_api_client(config):
    if config["api_client"] == "campcaster":	
        return campcaster_api_client.CampcasterApiClient(config)
    elif config["api_client"] == "obp":
        return obp_api_client.ObpApiClient(config)
		