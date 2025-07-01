import axios from 'axios'

const BASE = 'http://192.168.0.14:8000/api/rover'

export const startMissionAPI = async ({ x, y, direction }) => {
  const res = await axios.post(`${BASE}/start`, { x, y, direction })
  return res.data
}

export const executeMissionAPI = async ({ x, y, direction, commands, obstacles }) => {
  const res = await axios.post(`${BASE}/execute`, { x, y, direction, commands, obstacles })
  return res.data
}
