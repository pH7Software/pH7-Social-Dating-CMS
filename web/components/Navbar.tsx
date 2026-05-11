"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { Heart, Home, MessageCircle, Star, User, Settings } from "lucide-react";
import { cn } from "@/lib/utils";

const navItems = [
  { href: "/dashboard", label: "Discover", icon: Home },
  { href: "/matches", label: "Matches", icon: Star },
  { href: "/messages", label: "Messages", icon: MessageCircle },
  { href: "/profile", label: "Profile", icon: User },
];

export function AppNav() {
  const pathname = usePathname();
  return (
    <aside className="fixed left-0 top-0 h-full w-64 bg-card border-r border-border flex flex-col z-50">
      <div className="p-6 border-b border-border">
        <Link href="/dashboard" className="flex items-center gap-2">
          <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
            <Heart className="w-4 h-4 text-primary-foreground" />
          </div>
          <span className="font-bold text-lg text-foreground">pH7Builder</span>
        </Link>
      </div>
      <nav className="flex-1 p-4 space-y-1">
        {navItems.map(({ href, label, icon: Icon }) => (
          <Link
            key={href}
            href={href}
            className={cn(
              "flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all",
              pathname === href
                ? "bg-primary text-primary-foreground"
                : "text-muted-foreground hover:text-foreground hover:bg-accent"
            )}
          >
            <Icon className="w-5 h-5" />
            {label}
          </Link>
        ))}
      </nav>
      <div className="p-4 border-t border-border">
        <Link
          href="/settings"
          className="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-muted-foreground hover:text-foreground hover:bg-accent transition-all"
        >
          <Settings className="w-5 h-5" />
          Settings
        </Link>
        <div className="mt-3 flex items-center gap-3 px-4 py-3">
          <div className="w-9 h-9 rounded-full bg-primary/20 flex items-center justify-center text-primary font-bold text-sm">
            A
          </div>
          <div className="flex-1 min-w-0">
            <p className="text-sm font-medium text-foreground truncate">Alex Johnson</p>
            <p className="text-xs text-muted-foreground">Premium Member</p>
          </div>
        </div>
      </div>
    </aside>
  );
}
