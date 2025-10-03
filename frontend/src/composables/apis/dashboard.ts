import { apiService } from '@/services/api.ts'
import type {ApiResult} from "@/helpers/types/api";
import type { RawAxiosResponseHeaders, AxiosHeaders } from "axios";
import type { DashboardResponseDTO } from '@/composables/dtos/dashboard.ts'

export const useDashboardAPI = () => {

  const FetchDashboardInfo = async (): Promise<ApiResult<DashboardResponseDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.get<DashboardResponseDTO>("/dashboard")
      return { success: true, data: response.data, headers: response.headers }
    } catch (error) {
      return { success: false, error: "Unable to fetch dashboard info" }
    }
  }
  return {
    FetchDashboardInfo,
  }
}
