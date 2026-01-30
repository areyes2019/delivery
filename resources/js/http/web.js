import axios from 'axios'

const web = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest'
  }
})

export default web
