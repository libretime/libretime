import api from '../api';

class PlayoutHistoryService {
  getAll() {
    return api.get('/playout-history')
  }
}

export default new PlayoutHistoryService();
