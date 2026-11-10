export interface MenuItem {
  label: string
  icon: string
  to?: string
  children?: MenuItem[]
}

export function useMenuSidebar() {
  const navItems: MenuItem[] = [
    {
      label: 'Beranda',
      icon: 'i-lucide-house',
      to: '/'
    },
  ]

  const settingsItems: MenuItem[] = [
    {
      label: 'devices',
      icon: 'i-lucide-monitor', 
      to: '/setting/devices'
    },
    {
      label: 'contents',
      icon: 'i-lucide-folder-kanban',
      to: '/setting/contents'
    },
  ]

  return {
    navItems,
    settingsItems
  }
}