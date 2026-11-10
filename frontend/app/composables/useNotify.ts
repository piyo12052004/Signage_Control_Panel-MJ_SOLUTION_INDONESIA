export const useNotify = () => {
  const toast = useToast()

  const success = (message: string, title = 'Berhasil') => {
    toast.add({
      title,
      description: message,
      color: 'success',
      icon: 'i-lucide-check-circle'
    })
  }

  const error = (message: string, title = 'Terjadi Kesalahan') => {
    toast.add({
      title,
      description: message,
      color: 'error',
      icon: 'i-lucide-circle-x'
    })
  }

  const warning = (message: string, title = 'Perhatian') => {
    toast.add({
      title,
      description: message,
      color: 'warning',
      icon: 'i-lucide-triangle-alert'
    })
  }

  const info = (message: string, title = 'Informasi') => {
    toast.add({
      title,
      description: message,
      color: 'info',
      icon: 'i-lucide-info'
    })
  }

  return {
    success,
    error,
    warning,
    info
  }
}