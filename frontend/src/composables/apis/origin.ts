import { apiService } from '@/services/api.ts'
import type { ApiResult } from "@/helpers/types/api"
import type { RawAxiosResponseHeaders, AxiosHeaders } from "axios"
import type { CreateOriginDTO, OriginDTO, UpdateOriginDTO } from '@/composables/dtos/origin.ts'
import { notify } from '@/helpers/notification.ts'

export const useOriginsAPI = () => {
  const DeleteOrigin = async (
    id: string
  ): Promise<ApiResult<null, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.delete(`/origins/${id}`)
      notify(response.data.title || "Origin deleted successfully", "success")
      return { success: true, data: null, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Delete Origin failed", "error")
      return { success: false, error: error.response?.data?.title || "Delete Origin failed" }
    }
  }

  const CreateOrigin = async (
    createOriginDto: CreateOriginDTO
  ): Promise<ApiResult<OriginDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.post(`/origins`, createOriginDto)
      notify(`Origin created successfully.`, "success")
      return { success: true, data: response.data.data, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Create Origin failed", "error")
      return { success: false, error: error.response?.data?.title || "Create Origin failed" }
    }
  }

  const UpdateOrigin = async (
    id: string,
    updateOriginDto: UpdateOriginDTO
  ): Promise<ApiResult<OriginDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.patch(`/origins/${id}`, updateOriginDto)
      notify(`Origin updated successfully.`, "success")
      return { success: true, data: response.data.data, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Update Origin failed", "error")
      return { success: false, error: error.response?.data?.title || "Update Origin failed" }
    }
  }

  return {
    DeleteOrigin,
    UpdateOrigin,
    CreateOrigin
  }
}
