import { apiService } from '@/services/api.ts'
import type { ApiResult } from "@/helpers/types/api"
import type { RawAxiosResponseHeaders, AxiosHeaders } from "axios"
import type { CreateDestinationDTO, DestinationDTO, UpdateDestinationDTO } from '@/composables/dtos/destination.ts'
import { notify } from '@/helpers/notification.ts'

export const useDestinationAPI = () => {
  const DeleteDestination = async (
    id: string
  ): Promise<ApiResult<null, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.delete(`/destinations/${id}`)
      notify(response.data.title || "Destination deleted successfully", "success")
      return { success: true, data: null, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Delete Destination failed", "error")
      return { success: false, error: error.response?.data?.title || "Delete Destination failed" }
    }
  }

  const CreateDestination = async (
    createDestinationDto: CreateDestinationDTO
  ): Promise<ApiResult<DestinationDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.post(`/destinations`, createDestinationDto)
      notify(`Destination created successfully.`, "success")
      return { success: true, data: response.data.data, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Create Destination failed", "error")
      return { success: false, error: error.response?.data?.title || "Create Destination failed" }
    }
  }

  const UpdateDestination = async (
    id: string,
    updateDestinationDto: UpdateDestinationDTO
  ): Promise<ApiResult<DestinationDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.patch(`/destinations/${id}`, updateDestinationDto)
      notify(`Destination updated successfully.`, "success")
      return { success: true, data: response.data.data, headers: response.headers }
    } catch (error: any) {
      notify(error.response?.data?.title || "Update Destination failed", "error")
      return { success: false, error: error.response?.data?.title || "Update Destination failed" }
    }
  }

  return {
    DeleteDestination,
    UpdateDestination,
    CreateDestination,
  }
}
