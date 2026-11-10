
import type { ApiResponse } from '~/types/type-api'

export interface User {
    id: string
    name: string
    email: string
    created_at?: string
    updated_at?: string
}

export interface LoginPayload {
    email: string
    password: string
}

export interface AuthToken {
    access_token: string
    token_type: 'Bearer'
    expires_in: number
}

export interface LoginData extends AuthToken {
    user: User
}

export type LoginResponse = ApiResponse<LoginData>
export type TokenResponse = ApiResponse<AuthToken>
export type MeResponse = ApiResponse<User>
