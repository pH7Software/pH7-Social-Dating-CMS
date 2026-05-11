import React from 'react';
import { Heart, MessageCircle, Video, Shield, Check, Play, ChevronRight, Star, Users, Globe } from 'lucide-react';

export default function Landing() {
  return (
    <div className="min-h-screen bg-[#09090b] text-slate-50 font-sans selection:bg-rose-500/30">
      {/* Navbar */}
      <header className="sticky top-0 z-50 w-full border-b border-white/10 bg-background/80 backdrop-blur-md">
        <div className="container mx-auto px-4 h-16 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center">
              <Heart className="w-5 h-5 text-white" fill="currentColor" />
            </div>
            <span className="text-xl font-bold tracking-tight">pH7Builder</span>
          </div>
          <nav className="hidden md:flex gap-6 text-sm font-medium text-slate-300">
            <a href="#features" className="hover:text-rose-400 transition-colors">Features</a>
            <a href="#stats" className="hover:text-rose-400 transition-colors">Community</a>
            <a href="#pricing" className="hover:text-rose-400 transition-colors">Pricing</a>
          </nav>
          <div className="flex items-center gap-4">
            <a href="#login" className="text-sm font-medium text-slate-300 hover:text-white transition-colors">Log in</a>
            <a href="#signup" className="text-sm font-medium bg-rose-600 hover:bg-rose-700 text-white px-4 py-2 rounded-full transition-all shadow-[0_0_15px_rgba(225,29,72,0.3)]">
              Get Started
            </a>
          </div>
        </div>
      </header>

      {/* Hero Section */}
      <section className="relative pt-32 pb-20 md:pt-48 md:pb-32 overflow-hidden">
        <div className="absolute inset-0 top-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-rose-900/20 via-[#09090b] to-[#09090b] -z-10" />
        
        <div className="container mx-auto px-4 grid lg:grid-cols-2 gap-12 items-center">
          <div className="flex flex-col gap-8 max-w-2xl">
            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm font-medium w-fit">
              <SparklesIcon className="w-4 h-4" />
              <span>The #1 Social Dating Platform</span>
            </div>
            
            <h1 className="text-5xl md:text-7xl font-extrabold tracking-tight leading-[1.1]">
              Find Your <br />
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-rose-400 to-pink-600">
                Perfect Match
              </span>
            </h1>
            
            <p className="text-lg md:text-xl text-slate-400 leading-relaxed">
              Join millions of singles finding real connections. Advanced matching algorithms, 
              secure video dates, and a vibrant community waiting for you.
            </p>
            
            <div className="flex flex-col sm:flex-row gap-4 pt-4">
              <button className="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-rose-600 to-pink-600 hover:from-rose-500 hover:to-pink-500 text-white px-8 py-4 rounded-full font-semibold text-lg transition-all shadow-[0_0_30px_rgba(225,29,72,0.3)] hover:shadow-[0_0_40px_rgba(225,29,72,0.5)] hover:-translate-y-1">
                Get Started Free
                <ChevronRight className="w-5 h-5" />
              </button>
              <button className="inline-flex items-center justify-center gap-2 bg-white/5 hover:bg-white/10 text-white px-8 py-4 rounded-full font-semibold text-lg transition-all border border-white/10">
                <Play className="w-5 h-5" />
                Watch Demo
              </button>
            </div>
            
            <div className="flex items-center gap-4 pt-4 text-sm text-slate-400">
              <div className="flex -space-x-2">
                {[1, 2, 3, 4].map((i) => (
                  <div key={i} className="w-8 h-8 rounded-full bg-slate-800 border-2 border-[#09090b] flex items-center justify-center text-xs overflow-hidden">
                    <img src={`https://api.dicebear.com/7.x/avataaars/svg?seed=${i}&backgroundColor=e11d48`} alt="User" />
                  </div>
                ))}
              </div>
              <p>Trusted by <strong className="text-white">5M+</strong> users worldwide</p>
            </div>
          </div>
          
          <div className="relative lg:h-[600px] w-full rounded-2xl overflow-hidden shadow-[0_0_50px_rgba(225,29,72,0.15)] border border-white/10">
            <img 
              src="/__mockup/images/hero-dating.png" 
              alt="Dating App UI showing successful matches" 
              className="absolute inset-0 w-full h-full object-cover object-center"
            />
            {/* Overlay gradient for depth */}
            <div className="absolute inset-0 bg-gradient-to-t from-[#09090b] via-transparent to-transparent opacity-60"></div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section id="stats" className="py-12 border-y border-white/5 bg-white/[0.02]">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-3 gap-8 text-center divide-x divide-white/5">
            <div className="flex flex-col items-center gap-2">
              <Users className="w-8 h-8 text-rose-500 mb-2" />
              <h3 className="text-4xl font-bold">5M+</h3>
              <p className="text-slate-400 font-medium">Active Members</p>
            </div>
            <div className="flex flex-col items-center gap-2">
              <Globe className="w-8 h-8 text-rose-500 mb-2" />
              <h3 className="text-4xl font-bold">120+</h3>
              <p className="text-slate-400 font-medium">Countries</p>
            </div>
            <div className="flex flex-col items-center gap-2 col-span-2 md:col-span-1 border-t border-white/5 pt-8 md:border-t-0 md:pt-0">
              <Star className="w-8 h-8 text-rose-500 mb-2" />
              <h3 className="text-4xl font-bold">98%</h3>
              <p className="text-slate-400 font-medium">Satisfaction Rate</p>
            </div>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" className="py-24 relative">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-2xl mx-auto mb-16">
            <h2 className="text-3xl md:text-5xl font-bold mb-4">Everything You Need to <span className="text-rose-400">Connect</span></h2>
            <p className="text-slate-400 text-lg">Powerful features designed to spark meaningful conversations and build real relationships.</p>
          </div>
          
          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            <FeatureCard 
              icon={<Heart />}
              title="Instant Matching"
              description="Our proprietary algorithm finds highly compatible partners based on your shared interests and values."
            />
            <FeatureCard 
              icon={<MessageCircle />}
              title="Live Chat & Voice"
              description="Break the ice instantly with built-in text, voice notes, and real-time translation features."
            />
            <FeatureCard 
              icon={<Video />}
              title="Video Profiles"
              description="Get a better feel for personality before the first date with short, expressive video prompts."
            />
            <FeatureCard 
              icon={<Shield />}
              title="Privacy First"
              description="End-to-end encryption, mandatory photo verification, and robust blocking tools keep you safe."
            />
          </div>
        </div>
      </section>

      {/* Pricing Section */}
      <section id="pricing" className="py-24 bg-white/[0.02] border-t border-white/5">
        <div className="container mx-auto px-4">
          <div className="text-center max-w-2xl mx-auto mb-16">
            <h2 className="text-3xl md:text-5xl font-bold mb-4">Simple, Transparent <span className="text-rose-400">Pricing</span></h2>
            <p className="text-slate-400 text-lg">Start for free, upgrade when you're ready for more connections.</p>
          </div>

          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <PricingCard 
              title="Free"
              price="$0"
              description="Perfect for looking around."
              features={[
                "Basic profile creation",
                "Up to 10 matches per day",
                "Send 5 messages daily",
                "Standard search filters"
              ]}
              buttonText="Current Plan"
              variant="default"
            />
            <PricingCard 
              title="Premium"
              price="$9"
              period="/mo"
              description="Our most popular plan."
              features={[
                "Unlimited matches",
                "Unlimited messaging",
                "See who liked you",
                "Read receipts",
                "Advanced filters & incognito"
              ]}
              buttonText="Upgrade to Premium"
              variant="popular"
            />
            <PricingCard 
              title="Elite"
              price="$29"
              period="/mo"
              description="For the serious dater."
              features={[
                "Everything in Premium",
                "Profile boost (3x visibility)",
                "Priority customer support",
                "Send gifts & super likes",
                "Video dating access"
              ]}
              buttonText="Get Elite Access"
              variant="premium"
            />
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-24 relative overflow-hidden">
        <div className="absolute inset-0 bg-gradient-to-b from-transparent to-rose-900/20 -z-10" />
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl md:text-6xl font-bold mb-6">Ready to find love?</h2>
          <p className="text-xl text-slate-400 mb-10 max-w-2xl mx-auto">Join the fastest growing dating community today. It only takes 2 minutes to create your profile.</p>
          <button className="bg-rose-600 hover:bg-rose-700 text-white px-10 py-5 rounded-full font-bold text-xl transition-all shadow-[0_0_30px_rgba(225,29,72,0.4)] hover:shadow-[0_0_50px_rgba(225,29,72,0.6)] hover:scale-105">
            Create Your Free Account
          </button>
        </div>
      </section>

      {/* Footer */}
      <footer className="bg-[#050505] border-t border-white/10 pt-16 pb-8">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
            <div className="col-span-2">
              <div className="flex items-center gap-2 mb-6">
                <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-500 to-pink-600 flex items-center justify-center">
                  <Heart className="w-5 h-5 text-white" fill="currentColor" />
                </div>
                <span className="text-2xl font-bold tracking-tight">pH7Builder</span>
              </div>
              <p className="text-slate-400 max-w-sm">
                Building meaningful connections through technology. We believe everyone deserves to find their perfect match.
              </p>
            </div>
            
            <div>
              <h4 className="font-semibold mb-4 text-white">Company</h4>
              <ul className="space-y-3 text-slate-400 text-sm">
                <li><a href="#" className="hover:text-rose-400 transition-colors">About Us</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Careers</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Press</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Contact</a></li>
              </ul>
            </div>
            
            <div>
              <h4 className="font-semibold mb-4 text-white">Legal</h4>
              <ul className="space-y-3 text-slate-400 text-sm">
                <li><a href="#" className="hover:text-rose-400 transition-colors">Terms of Service</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Privacy Policy</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Cookie Policy</a></li>
                <li><a href="#" className="hover:text-rose-400 transition-colors">Safety Tips</a></li>
              </ul>
            </div>
          </div>
          
          <div className="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-sm text-slate-500">
            <p>© {new Date().getFullYear()} pH7Builder. All rights reserved.</p>
            <div className="flex gap-4">
              <a href="#" className="hover:text-white transition-colors">Twitter</a>
              <a href="#" className="hover:text-white transition-colors">Instagram</a>
              <a href="#" className="hover:text-white transition-colors">Facebook</a>
            </div>
          </div>
        </div>
      </footer>
    </div>
  );
}

function FeatureCard({ icon, title, description }: { icon: React.ReactNode, title: string, description: string }) {
  return (
    <div className="p-6 rounded-2xl bg-white/[0.03] border border-white/5 hover:bg-white/[0.05] transition-colors group">
      <div className="w-12 h-12 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center mb-6 group-hover:scale-110 group-hover:bg-rose-500/20 transition-all">
        {icon}
      </div>
      <h3 className="text-xl font-semibold mb-3">{title}</h3>
      <p className="text-slate-400 leading-relaxed">{description}</p>
    </div>
  );
}

function PricingCard({ 
  title, 
  price, 
  period = "", 
  description, 
  features, 
  buttonText, 
  variant 
}: { 
  title: string, 
  price: string, 
  period?: string, 
  description: string, 
  features: string[], 
  buttonText: string, 
  variant: 'default' | 'popular' | 'premium' 
}) {
  const isPopular = variant === 'popular';
  
  return (
    <div className={`relative p-8 rounded-3xl border flex flex-col h-full ${
      isPopular 
        ? 'bg-gradient-to-b from-rose-900/20 to-[#09090b] border-rose-500/50 shadow-[0_0_30px_rgba(225,29,72,0.1)]' 
        : 'bg-white/[0.02] border-white/10'
    }`}>
      {isPopular && (
        <div className="absolute -top-4 left-1/2 -translate-x-1/2 bg-gradient-to-r from-rose-500 to-pink-500 text-white px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
          Most Popular
        </div>
      )}
      
      <div className="mb-8">
        <h3 className="text-2xl font-bold mb-2">{title}</h3>
        <div className="flex items-baseline gap-1 mb-4">
          <span className="text-5xl font-extrabold">{price}</span>
          <span className="text-slate-400 font-medium">{period}</span>
        </div>
        <p className="text-slate-400">{description}</p>
      </div>
      
      <ul className="space-y-4 mb-8 flex-1">
        {features.map((feature, i) => (
          <li key={i} className="flex items-start gap-3">
            <Check className="w-5 h-5 text-rose-500 shrink-0 mt-0.5" />
            <span className="text-slate-300">{feature}</span>
          </li>
        ))}
      </ul>
      
      <button className={`w-full py-4 rounded-xl font-bold transition-all ${
        isPopular 
          ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-[0_0_20px_rgba(225,29,72,0.3)]' 
          : 'bg-white/10 hover:bg-white/20 text-white'
      }`}>
        {buttonText}
      </button>
    </div>
  );
}

function SparklesIcon(props: React.SVGProps<SVGSVGElement>) {
  return (
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinelinejoin="round" {...props}>
      <path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/>
      <path d="M5 3v4"/>
      <path d="M19 17v4"/>
      <path d="M3 5h4"/>
      <path d="M17 19h4"/>
    </svg>
  );
}
