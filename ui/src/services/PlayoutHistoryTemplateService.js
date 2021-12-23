import api from '../api'

class PlayoutHistoryTemplateService {
  getAll() {
    return api.get('/playout-history-templates')
  }
}

export default new PlayoutHistoryTemplateService()
