import { AppNav } from "@/components/Navbar";
import { MapPin, CheckCircle, Star, Heart, X, ChevronLeft, MoreVertical, Edit } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import { Separator } from "@/components/ui/separator";
import Link from "next/link";

const interests = ["Travel", "Cooking", "Photography", "Hiking", "Live Music", "Coffee", "Dogs", "Mario Kart"];

const galleryColors = ["#7c3aed", "#db2777", "#059669", "#d97706", "#2563eb", "#dc2626"];

export default function ProfilePage() {
  return (
    <div className="min-h-screen bg-background flex">
      <AppNav />
      <main className="ml-64 flex-1 overflow-y-auto pb-24">
        {/* Header */}
        <div className="sticky top-0 z-40 bg-background/80 backdrop-blur border-b border-border px-6 h-14 flex items-center justify-between">
          <Link href="/dashboard">
            <Button variant="ghost" size="sm" className="text-muted-foreground">
              <ChevronLeft className="w-4 h-4 mr-1" /> Back
            </Button>
          </Link>
          <div className="flex items-center gap-2">
            <Button variant="ghost" size="icon"><MoreVertical className="w-5 h-5 text-muted-foreground" /></Button>
          </div>
        </div>

        <div className="max-w-2xl mx-auto px-6 py-8 space-y-6">
          {/* Profile header card */}
          <div className="bg-card border border-border rounded-3xl overflow-hidden">
            {/* Cover */}
            <div className="h-40 bg-gradient-to-br from-purple-900 via-primary/50 to-background relative">
              <div className="absolute inset-0 bg-gradient-to-t from-card/80 to-transparent" />
            </div>

            {/* Avatar + info */}
            <div className="px-6 pb-6 -mt-16 relative">
              <div className="flex items-end justify-between mb-4">
                <div className="relative">
                  <div className="w-28 h-28 rounded-full border-4 border-card bg-gradient-to-br from-primary to-purple-600 flex items-center justify-center text-4xl font-bold text-white shadow-xl">
                    E
                  </div>
                  <div className="absolute bottom-2 right-2 w-5 h-5 bg-green-500 rounded-full border-2 border-card" />
                </div>
                <div className="flex items-center gap-2 mb-2">
                  <div className="text-center px-4 py-2 rounded-xl bg-primary/10 border border-primary/20">
                    <p className="text-2xl font-bold text-primary">87%</p>
                    <p className="text-xs text-muted-foreground">MATCH</p>
                  </div>
                </div>
              </div>

              <div className="space-y-2">
                <div className="flex items-center gap-2">
                  <h1 className="text-2xl font-bold text-foreground">Elena, 26</h1>
                  <CheckCircle className="w-5 h-5 text-primary" />
                </div>
                <div className="flex items-center gap-1.5 text-muted-foreground text-sm">
                  <MapPin className="w-4 h-4" />
                  San Francisco, CA • 2 miles away
                </div>
              </div>
            </div>
          </div>

          {/* Stats */}
          <div className="grid grid-cols-3 gap-4">
            {[{ v: "142", l: "Matches" }, { v: "89", l: "Likes" }, { v: "4.9 ★", l: "Rating" }].map(({ v, l }) => (
              <div key={l} className="bg-card border border-border rounded-2xl p-4 text-center">
                <p className="text-2xl font-bold text-foreground">{v}</p>
                <p className="text-xs text-muted-foreground mt-1 uppercase tracking-wide">{l}</p>
              </div>
            ))}
          </div>

          {/* About */}
          <div className="bg-card border border-border rounded-2xl p-6 space-y-3">
            <h2 className="font-bold text-foreground">About Me</h2>
            <p className="text-muted-foreground text-sm leading-relaxed">
              Software engineer by day, amateur chef by night. I love exploring the city for the best hidden coffee spots and taking weekend hiking trips to clear my mind. Looking for someone who can match my energy and isn&apos;t afraid of a little friendly competition in Mario Kart. ☕🌿⛰️
            </p>
          </div>

          {/* Interests */}
          <div className="bg-card border border-border rounded-2xl p-6 space-y-4">
            <h2 className="font-bold text-foreground">Interests</h2>
            <div className="flex flex-wrap gap-2">
              {interests.map((interest) => (
                <Badge key={interest} variant="secondary" className="px-3 py-1.5 text-sm">
                  {interest}
                </Badge>
              ))}
            </div>
          </div>

          {/* Photos */}
          <div className="bg-card border border-border rounded-2xl p-6 space-y-4">
            <div className="flex items-center justify-between">
              <h2 className="font-bold text-foreground">Photos</h2>
              <button className="text-sm text-primary hover:underline">View All</button>
            </div>
            <div className="grid grid-cols-3 gap-3">
              {galleryColors.map((color, i) => (
                <div key={i} className="aspect-square rounded-xl flex items-center justify-center text-white font-bold text-2xl" style={{ background: `linear-gradient(135deg, ${color}, ${color}88)` }}>
                  {i + 1}
                </div>
              ))}
            </div>
          </div>

          <Separator className="bg-border" />

          {/* Edit profile button */}
          <Link href="/profile/edit">
            <Button variant="outline" className="w-full border-border hover:bg-accent h-12">
              <Edit className="w-4 h-4 mr-2" /> Edit Profile
            </Button>
          </Link>
        </div>

        {/* Sticky action bar */}
        <div className="fixed bottom-0 left-64 right-0 bg-background/90 backdrop-blur border-t border-border px-8 py-4 flex items-center justify-center gap-6">
          <button className="w-14 h-14 rounded-full bg-card border border-border flex items-center justify-center hover:border-destructive hover:bg-destructive/10 transition-all shadow-lg">
            <X className="w-6 h-6 text-muted-foreground" />
          </button>
          <button className="w-12 h-12 rounded-full bg-card border border-yellow-500/50 flex items-center justify-center hover:bg-yellow-500/10 transition-all shadow-md">
            <Star className="w-5 h-5 text-yellow-400" />
          </button>
          <button className="w-14 h-14 rounded-full bg-primary flex items-center justify-center hover:bg-primary/90 transition-all shadow-lg shadow-primary/30">
            <Heart className="w-6 h-6 text-primary-foreground" />
          </button>
        </div>
      </main>
    </div>
  );
}
