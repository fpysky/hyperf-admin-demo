export namespace Admin {
  export interface AdminDto{
    id: number,
    name: string,
    mobile: string,
    password: string,
    rePassword: string,
    email: string,
    status: number,
    roleIds: number[],
  }
}
