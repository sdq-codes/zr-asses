import axios, { type AxiosInstance, type AxiosResponse, AxiosError } from 'axios'

class ApiService {
  api: AxiosInstance
  private readonly baseURL: string

  constructor() {
    this.baseURL = import.meta.env.VITE_API_BASE_URL

    this.api = axios.create({
      baseURL: this.baseURL,
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json',
      },
    })

    this.setupInterceptors()
  }

  private setupInterceptors() {
    this.api.interceptors.request.use(
      (config) => {
        const token = localStorage.getItem('auth_token')
        if (token) {
          config.headers.Authorization = `Bearer ${token}`
        }
        return config
      },
      (error) => {
        return Promise.reject(error)
      }
    )

    this.api.interceptors.response.use(
      (response: AxiosResponse) => {
        return response
      },
      (error: AxiosError) => {
        return Promise.reject(error)
      }
    )
  }
}

export const apiService = new ApiService()
