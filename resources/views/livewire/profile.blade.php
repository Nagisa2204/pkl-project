<x-app-layout>
    <div class="py-12" x-data="{activeTab: 'profile'}">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="ui-card bg-accent p-4 sm:p-8">
                <div class="max-w rounded-lg mb-6">
                    <h1 class="text-2xl font-extrabold text-primary">Profil</h1>
                    <h2 class="mt-4 text-2xl font-extrabold text-primary-foreground">{{ Auth::user()->name }}</h2>
                    <p class="mt-1 text-sm text-primary-foreground">{{ Auth::user()->email }}</p>
                </div>
                <div class="flex items-center p-1 bg-accent-hover space-x-1 border rounded-xl border-primary w-max">
                    <button @click="activeTab = 'profile'" :class="{'bg-primary text-content': activeTab === 'profile', 'text-primary-foreground': activeTab !== 'profile'}" class="px-4 py-2 rounded-lg font-semibold focus:outline-none">Profil</button>
                    <button @click="activeTab = 'security'" :class="{'bg-primary text-content': activeTab === 'security', 'text-primary-foreground': activeTab !== 'security'}" class="px-4 py-2 rounded-lg font-semibold focus:outline-none">Security</button>
                </div>
            </div>
               
            <div class="col-span-2">
                <div x-show="activeTab === 'profile'" x-cloak class="grid grid-cols-2 gap-4">
                    <div class="ui-card p-4 sm:p-8">
                        <div class="max-w-xl">
                            <livewire:profile.update-profile-information-form />
                        </div>
                    </div>
                    <div class="ui-card p-4 sm:p-8">
                        <div class="max-w-xl">
                            <livewire:profile.update-user-location />
                        </div>
                    </div>       
                </div>

                <div x-show="activeTab === 'security'" x-cloak class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div class="ui-card p-4 sm:p-8">
                        <div class="max-w-xl">
                            <livewire:profile.update-password-form />
                        </div>
                    </div>
                    <div class="ui-card p-4 sm:p-8">
                        <div class="max-w-xl">
                            <livewire:profile.delete-user-form />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
