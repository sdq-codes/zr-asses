import { apiService } from '@/services/api.ts'
import type {ApiResult} from "@/helpers/types/api";
import type { RawAxiosResponseHeaders, AxiosHeaders } from "axios";
import type { CreateTaskDTO, TaskDTO, UpdateTaskDTO } from '@/composables/dtos/Task.ts'
import { notify } from '@/helpers/notification.ts'

export const useTasksAPI = () => {

  const DeleteTask = async (id: string): Promise<ApiResult<null, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.delete(`/tasks/${id}`)
      notify(response.data.title, "success")
      return { success: true, data: null, headers: response.headers }
    } catch (error) {
      notify(error.response?.data?.title, "error")
      return { success: false, error: error.response?.data?.title || "Delete Task Failed" }
    }
  }

  const UpdateTask = async (id: string, updateTaskDto: UpdateTaskDTO, destinationName: string, originName: string): Promise<ApiResult<null, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.patch(`/tasks/${id}`, updateTaskDto)
      notify(`You saved ${originName} and ${destinationName}.`, "success")
      return { success: true, data: null, headers: response.headers }
    } catch (error) {
      notify(`Error connecting ${originName} to ${destinationName}.`, "error")
      return { success: false, error: error.response?.data?.title || "Update Task failed" }
    }
  }

  const CreateTask = async (createTaskDto: CreateTaskDTO, destinationName: string, originName: string): Promise<ApiResult<TaskDTO, RawAxiosResponseHeaders | (RawAxiosResponseHeaders & AxiosHeaders)>> => {
    try {
      const response = await apiService.api.post(`/tasks`, createTaskDto)
      notify(`You saved ${originName} and ${destinationName}.`, "success")
      return { success: true, data: response.data.data, headers: response.headers }
    } catch (error) {
      notify(`Error connecting ${originName} to ${destinationName}.`, "error")
      return { success: false, error: error.response?.data?.title || "Update Task failed" }
    }
  }

  return {
    DeleteTask,
    UpdateTask,
    CreateTask
  }
}
