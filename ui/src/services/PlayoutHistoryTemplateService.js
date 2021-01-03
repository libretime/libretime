import api from '../api'

class PlayoutHistoryTemplateService {
  getAll() {
    return api.get('/playout-history-template')
  }
}

export default new PlayoutHistoryTemplateService()
