export const useSidebar = () => {
  const isOpen = useState<boolean>('sidebar-is-open', () => true)

  const toggle = () => {
    isOpen.value = !isOpen.value
  }

  return {
    isOpen,
    toggle
  }
}