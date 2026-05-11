"use client";
import { useState } from "react";
import { AppNav } from "@/components/Navbar";
import { Bell, Search, Star, X, Heart, MapPin, GraduationCap, Briefcase } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Badge } from "@/components/ui/badge";

const profiles = [
  { id: 1, name: "Elena", age: 24, job: "UX Designer", location: "New York, NY", distance: "2 miles away", education: "NYU", bio: "Coffee addict, amateur photographer, and professional dog petter. Looking for someone to explore the city with.", match: 94, interests: ["Coffee", "Photography", "Hiking", "Art"], color: "#7c3aed" },
  { id: 2, name: "Sophia", age: 26, job: "Marketing Lead", location: "Brooklyn, NY", distance: "4 miles away", education: "Columbia", bio: "Bookworm by day, salsa dancer by night. Passionate about travel and trying every cuisine in NYC.", match: 87, interests: ["Travel", "Dancing", "Books", "Food"], color: "#db2777" },
  { id: 3, name: "Maya", age: 23, job: "Software Engineer", location: "Manhattan, NY", distance: "1 mile away", education: "MIT", bio: "Tech by day, painting by night. I speak three languages and can solve a Rubik's cube in 45 seconds.", match: 91, interests: ["Tech", "Art", "Puzzles", "Music"], color: "#059669" },
];

const matches = [
  { name: "Jessica", online: true, color: "#db2777" },
  { name: "Amanda", online: false, color: "#7c3aed" },
  { name: "Rachel", online: true, color: "#d97706" },
  { name: "Chris", online: true, color: "#2563eb" },
];

const activity = [
  { text: "Jessica sent you a message", time: "2m ago", icon: "💬" },
  { text: "You have a new match!", time: "1h ago", icon: "💖" },
  { text: "Someone liked your profile", time: "3h ago", icon: "❤️" },
  { text: "Your profile was boosted", time: "5h ago", icon: "🚀" },
];

export default function DashboardPage() {
  const [currentIndex, setCurrentIndex] = useState(0);
  const profile = profiles[currentIndex % profiles.length];

  const handleAction = () => {
    setCurrentIndex((i) => i + 1);
  };

  return (
    <div className="min-h-screen bg-background flex">
      <AppNav />
      <main className="ml-64 flex-1 flex flex-col">
        {/* Top bar */}
        <header className="h-16 border-b border-border flex items-center justify-between px-6 bg-background/80 backdrop-blur sticky top-0 z-40">
          <div className="relative w-72">
            <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
            <Input placeholder="Search matches or messages..." className="pl-9 bg-card border-border h-9 text-sm" />
          </div>
          <div className="flex items-center gap-3">
            <Button variant="ghost" size="icon" className="relative">
              <Bell className="w-5 h-5 text-muted-foreground" />
              <span className="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full" />
            </Button>
            <Button size="sm" className="bg-primary hover:bg-primary/90 text-primary-foreground font-medium">
              🔥 Boost Profile
            </Button>
          </div>
        </header>

        <div className="flex-1 flex overflow-hidden">
          {/* Main discovery area */}
          <div className="flex-1 flex items-center justify-center p-8">
            <div className="w-full max-w-sm">
              {/* Match badge */}
              <div className="flex justify-center mb-4">
                <Badge className="bg-yellow-500/20 text-yellow-400 border-yellow-500/30 px-4 py-1.5">
                  ⭐ {profile.match}% Match
                </Badge>
              </div>

              {/* Profile card */}
              <div className="bg-card border border-border rounded-3xl overflow-hidden shadow-2xl">
                {/* Photo area */}
                <div className="relative h-80 flex items-center justify-center" style={{ background: `linear-gradient(135deg, ${profile.color}33, ${profile.color}11)` }}>
                  <div className="w-32 h-32 rounded-full flex items-center justify-center text-5xl font-bold text-white" style={{ background: profile.color }}>
                    {profile.name[0]}
                  </div>
                  <div className="absolute bottom-4 left-4 right-4 bg-background/80 backdrop-blur rounded-2xl p-3">
                    <div className="flex items-center justify-between">
                      <div>
                        <h2 className="text-xl font-bold text-foreground">{profile.name}, {profile.age}</h2>
                        <p className="text-sm text-primary font-medium">{profile.job}</p>
                      </div>
                    </div>
                    <div className="mt-2 flex items-center gap-3 text-xs text-muted-foreground">
                      <span className="flex items-center gap-1"><MapPin className="w-3 h-3" />{profile.distance}</span>
                      <span className="flex items-center gap-1"><GraduationCap className="w-3 h-3" />{profile.education}</span>
                    </div>
                  </div>
                </div>

                <div className="p-5 space-y-4">
                  <div>
                    <p className="text-xs text-muted-foreground uppercase font-semibold mb-1">About Me</p>
                    <p className="text-sm text-muted-foreground leading-relaxed">{profile.bio}</p>
                  </div>
                  <div className="flex flex-wrap gap-2">
                    {profile.interests.map((i) => (
                      <Badge key={i} variant="secondary" className="text-xs">{i}</Badge>
                    ))}
                  </div>
                </div>
              </div>

              {/* Action buttons */}
              <div className="flex items-center justify-center gap-4 mt-6">
                <button onClick={handleAction} className="w-14 h-14 rounded-full bg-card border border-border flex items-center justify-center hover:border-destructive hover:bg-destructive/10 transition-all shadow-lg">
                  <X className="w-6 h-6 text-muted-foreground" />
                </button>
                <button onClick={handleAction} className="w-12 h-12 rounded-full bg-card border border-yellow-500/50 flex items-center justify-center hover:bg-yellow-500/10 transition-all shadow-md">
                  <Star className="w-5 h-5 text-yellow-400" />
                </button>
                <button onClick={handleAction} className="w-14 h-14 rounded-full bg-primary flex items-center justify-center hover:bg-primary/90 transition-all shadow-lg shadow-primary/30">
                  <Heart className="w-6 h-6 text-primary-foreground" />
                </button>
              </div>
            </div>
          </div>

          {/* Right panel */}
          <aside className="w-80 border-l border-border bg-card/50 flex flex-col overflow-y-auto">
            {/* New Matches */}
            <div className="p-5 border-b border-border">
              <div className="flex items-center justify-between mb-4">
                <h3 className="font-semibold text-foreground text-sm uppercase tracking-wide">New Matches</h3>
                <button className="text-xs text-primary hover:underline">See all</button>
              </div>
              <div className="flex gap-3">
                {matches.map((m) => (
                  <div key={m.name} className="flex flex-col items-center gap-1.5">
                    <div className="relative">
                      <div className="w-12 h-12 rounded-full flex items-center justify-center text-white font-bold text-sm" style={{ background: m.color }}>
                        {m.name[0]}
                      </div>
                      {m.online && <div className="absolute bottom-0.5 right-0.5 w-3 h-3 bg-green-500 rounded-full border-2 border-background" />}
                    </div>
                    <span className="text-xs text-muted-foreground">{m.name}</span>
                  </div>
                ))}
              </div>
            </div>

            {/* Activity */}
            <div className="p-5 flex-1">
              <h3 className="font-semibold text-foreground text-sm uppercase tracking-wide mb-4">Activity</h3>
              <div className="space-y-3">
                {activity.map((a) => (
                  <div key={a.text} className="flex items-center gap-3 p-3 rounded-xl hover:bg-accent transition-colors cursor-pointer">
                    <div className="w-9 h-9 rounded-full bg-background flex items-center justify-center text-lg flex-shrink-0">
                      {a.icon}
                    </div>
                    <div className="flex-1 min-w-0">
                      <p className="text-sm text-foreground leading-tight">{a.text}</p>
                      <p className="text-xs text-muted-foreground mt-0.5">{a.time}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Upgrade CTA */}
            <div className="p-5 border-t border-border">
              <div className="bg-gradient-to-br from-primary/20 to-purple-900/30 rounded-2xl p-4 border border-primary/20">
                <p className="text-sm font-bold text-foreground">🔥 Get more matches</p>
                <p className="text-xs text-muted-foreground mt-1 mb-3">Upgrade to Premium to see who liked you.</p>
                <Button size="sm" className="w-full bg-primary hover:bg-primary/90 text-primary-foreground text-xs">
                  Upgrade Now →
                </Button>
              </div>
            </div>
          </aside>
        </div>
      </main>
    </div>
  );
}
