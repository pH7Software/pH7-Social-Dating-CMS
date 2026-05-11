import Link from "next/link";
import { Heart, ArrowRight, MessageCircle, Shield, Video, Zap, Star, Globe, Users } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";

const features = [
  { icon: Zap, title: "Instant Matching", desc: "Smart algorithm finds your ideal matches in seconds based on personality, interests, and location." },
  { icon: MessageCircle, title: "Live Chat", desc: "Real-time messaging with read receipts, voice notes, and photo sharing built right in." },
  { icon: Video, title: "Video Profiles", desc: "Go beyond photos — record a 30-second intro video so your personality shines through." },
  { icon: Shield, title: "Privacy First", desc: "End-to-end encrypted messages, photo blur controls, and granular privacy settings." },
];

const stats = [
  { icon: Users, value: "5M+", label: "Members" },
  { icon: Globe, value: "120+", label: "Countries" },
  { icon: Star, value: "98%", label: "Satisfaction" },
  { icon: Heart, value: "2M+", label: "Couples Matched" },
];

const plans = [
  { name: "Free", price: "$0", period: "/mo", color: "border-border", features: ["10 daily likes", "Basic matching", "Limited messages", "View profiles"] },
  { name: "Premium", price: "$9", period: "/mo", color: "border-primary", popular: true, features: ["Unlimited likes", "Advanced filters", "Unlimited messages", "See who liked you", "Boost profile 1×/week"] },
  { name: "Elite", price: "$29", period: "/mo", color: "border-yellow-500", features: ["Everything in Premium", "Priority matching", "Read receipts", "Incognito mode", "5× weekly boosts", "VIP support"] },
];

export default function Home() {
  return (
    <div className="min-h-screen bg-background">
      {/* Navbar */}
      <nav className="fixed top-0 w-full z-50 bg-background/80 backdrop-blur-md border-b border-border">
        <div className="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
          <Link href="/" className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
              <Heart className="w-4 h-4 text-primary-foreground" />
            </div>
            <span className="font-bold text-lg">pH7Builder</span>
          </Link>
          <div className="hidden md:flex items-center gap-8 text-sm text-muted-foreground">
            <Link href="#features" className="hover:text-foreground transition-colors">Features</Link>
            <Link href="#pricing" className="hover:text-foreground transition-colors">Pricing</Link>
            <Link href="#" className="hover:text-foreground transition-colors">Blog</Link>
          </div>
          <div className="flex items-center gap-3">
            <Link href="/login">
              <Button variant="ghost" size="sm" className="text-muted-foreground hover:text-foreground">Sign In</Button>
            </Link>
            <Link href="/register">
              <Button size="sm" className="bg-primary hover:bg-primary/90 text-primary-foreground font-semibold">Get Started Free</Button>
            </Link>
          </div>
        </div>
      </nav>

      {/* Hero */}
      <section className="pt-32 pb-20 px-6">
        <div className="max-w-7xl mx-auto grid lg:grid-cols-2 gap-12 items-center">
          <div className="space-y-8">
            <Badge variant="secondary" className="text-primary border-primary/30 bg-primary/10 px-4 py-1.5">
              ✨ The #1 Social Dating Platform
            </Badge>
            <h1 className="text-5xl lg:text-7xl font-bold leading-tight">
              Find Your{" "}
              <span className="text-primary">Perfect Match</span>
            </h1>
            <p className="text-xl text-muted-foreground leading-relaxed max-w-lg">
              Join millions of singles finding real connections. Advanced matching algorithms, secure video dates, and a vibrant community waiting for you.
            </p>
            <div className="flex flex-wrap gap-4">
              <Link href="/register">
                <Button size="lg" className="bg-primary hover:bg-primary/90 text-primary-foreground font-semibold px-8 h-14 text-base">
                  Get Started Free <ArrowRight className="ml-2 w-5 h-5" />
                </Button>
              </Link>
              <Button size="lg" variant="outline" className="border-border hover:bg-accent h-14 text-base px-8">
                Watch Demo
              </Button>
            </div>
            <div className="flex items-center gap-3 text-sm text-muted-foreground">
              <div className="flex -space-x-2">
                {["#e91e8c", "#9c27b0", "#3f51b5", "#009688"].map((c, i) => (
                  <div key={i} className="w-8 h-8 rounded-full border-2 border-background" style={{ background: c }} />
                ))}
              </div>
              Trusted by <strong className="text-foreground">5M+</strong> users worldwide
            </div>
          </div>
          <div className="relative">
            <div className="absolute inset-0 bg-gradient-to-br from-primary/20 to-purple-600/20 rounded-3xl blur-3xl" />
            <div className="relative bg-card border border-border rounded-3xl overflow-hidden shadow-2xl">
              <div className="bg-gradient-to-br from-primary/30 via-purple-900/40 to-background p-8 space-y-4">
                {[
                  { name: "Elena, 24", job: "UX Designer", match: "94%", color: "#e91e8c" },
                  { name: "Sophia, 26", job: "Marketing Lead", match: "87%", color: "#9c27b0" },
                ].map((u) => (
                  <div key={u.name} className="bg-background/60 backdrop-blur rounded-2xl p-4 flex items-center gap-4 border border-white/10">
                    <div className="w-12 h-12 rounded-full flex-shrink-0 flex items-center justify-center text-white font-bold text-lg" style={{ background: u.color }}>
                      {u.name[0]}
                    </div>
                    <div className="flex-1">
                      <p className="font-semibold text-foreground">{u.name}</p>
                      <p className="text-sm text-muted-foreground">{u.job}</p>
                    </div>
                    <div className="text-right">
                      <p className="text-xs text-muted-foreground">Match</p>
                      <p className="font-bold text-primary">{u.match}</p>
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats */}
      <section className="py-16 px-6 border-y border-border">
        <div className="max-w-7xl mx-auto grid grid-cols-2 lg:grid-cols-4 gap-8">
          {stats.map(({ icon: Icon, value, label }) => (
            <div key={label} className="text-center space-y-2">
              <Icon className="w-6 h-6 text-primary mx-auto" />
              <p className="text-4xl font-bold text-foreground">{value}</p>
              <p className="text-muted-foreground">{label}</p>
            </div>
          ))}
        </div>
      </section>

      {/* Features */}
      <section id="features" className="py-24 px-6">
        <div className="max-w-7xl mx-auto space-y-16">
          <div className="text-center space-y-4">
            <h2 className="text-4xl lg:text-5xl font-bold">Everything you need to <span className="text-primary">connect</span></h2>
            <p className="text-xl text-muted-foreground max-w-2xl mx-auto">Built for modern dating — safe, fun, and genuinely effective.</p>
          </div>
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {features.map(({ icon: Icon, title, desc }) => (
              <div key={title} className="bg-card border border-border rounded-2xl p-6 space-y-4 hover:border-primary/50 transition-all group">
                <div className="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
                  <Icon className="w-6 h-6 text-primary" />
                </div>
                <h3 className="font-bold text-lg text-foreground">{title}</h3>
                <p className="text-muted-foreground text-sm leading-relaxed">{desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Pricing */}
      <section id="pricing" className="py-24 px-6 bg-card/30">
        <div className="max-w-5xl mx-auto space-y-16">
          <div className="text-center space-y-4">
            <h2 className="text-4xl lg:text-5xl font-bold">Simple, transparent <span className="text-primary">pricing</span></h2>
            <p className="text-xl text-muted-foreground">Start free. Upgrade when you're ready.</p>
          </div>
          <div className="grid md:grid-cols-3 gap-6">
            {plans.map((plan) => (
              <div key={plan.name} className={`relative bg-card rounded-2xl border-2 ${plan.color} p-8 space-y-6 ${plan.popular ? "scale-105 shadow-2xl shadow-primary/20" : ""}`}>
                {plan.popular && (
                  <Badge className="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary text-primary-foreground">Most Popular</Badge>
                )}
                <div>
                  <p className="font-bold text-foreground text-lg">{plan.name}</p>
                  <div className="flex items-end gap-1 mt-2">
                    <span className="text-4xl font-bold text-foreground">{plan.price}</span>
                    <span className="text-muted-foreground pb-1">{plan.period}</span>
                  </div>
                </div>
                <ul className="space-y-3">
                  {plan.features.map((f) => (
                    <li key={f} className="flex items-center gap-2 text-sm text-muted-foreground">
                      <div className="w-1.5 h-1.5 rounded-full bg-primary flex-shrink-0" />
                      {f}
                    </li>
                  ))}
                </ul>
                <Link href="/register">
                  <Button className={`w-full ${plan.popular ? "bg-primary hover:bg-primary/90 text-primary-foreground" : "variant-outline border-border hover:bg-accent"}`}>
                    Get Started
                  </Button>
                </Link>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="py-24 px-6">
        <div className="max-w-4xl mx-auto text-center space-y-8">
          <h2 className="text-4xl lg:text-6xl font-bold">Ready to find your <span className="text-primary">match?</span></h2>
          <p className="text-xl text-muted-foreground">Join 5 million singles already using pH7Builder.</p>
          <Link href="/register">
            <Button size="lg" className="bg-primary hover:bg-primary/90 text-primary-foreground font-bold px-12 h-14 text-lg">
              Create Free Account <ArrowRight className="ml-2 w-5 h-5" />
            </Button>
          </Link>
        </div>
      </section>

      {/* Footer */}
      <footer className="border-t border-border py-12 px-6">
        <div className="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
          <div className="flex items-center gap-2">
            <div className="w-7 h-7 rounded-full bg-primary flex items-center justify-center">
              <Heart className="w-3.5 h-3.5 text-primary-foreground" />
            </div>
            <span className="font-bold text-foreground">pH7Builder</span>
          </div>
          <div className="flex gap-6 text-sm text-muted-foreground">
            {["About", "Blog", "Careers", "Privacy", "Terms", "Support"].map((l) => (
              <Link key={l} href="#" className="hover:text-foreground transition-colors">{l}</Link>
            ))}
          </div>
          <p className="text-sm text-muted-foreground">© 2026 pH7Builder. All rights reserved.</p>
        </div>
      </footer>
    </div>
  );
}
