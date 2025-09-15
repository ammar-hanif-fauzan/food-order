<?php

namespace App\Livewire\Account\MyProfile;

use Livewire\Component;
use App\Models\Customer;
use Livewire\WithFileUploads;

class Index extends Component
{
    use WithFileUploads;

    public $image;
    public $name;
    public $email;
    public $currentImage;
    
    /**
     * mount
     *
     * @return void
     */
    public function mount()
    {
        $this->name = auth()->guard('customer')->user()->name;
        $this->email = auth()->guard('customer')->user()->email;
        $this->currentImage = auth()->guard('customer')->user()->image;
    }
    
    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,'. auth()->guard('customer')->user()->id,
        ];
    }

    public function update()
    {
        try {
            // Validasi input
            $this->validate();
            
            $profile = Customer::findOrFail(auth()->guard('customer')->user()->id);
            
            // Cek apakah ada gambar yang di-upload
            if ($this->image && is_object($this->image)) {
                // Upload gambar baru
                $imageName = $this->image->hashName();
                $this->image->storeAs('public/avatars', $imageName);

                // Update data pengguna dengan gambar
                $updated = $profile->update([
                    'name'  => $this->name,
                    'email' => $this->email,
                    'image' => $imageName,
                ]);

                if ($updated) {
                    // Update currentImage untuk ditampilkan
                    $this->currentImage = $imageName;
                    
                    // Reset image property setelah upload
                    $this->image = null;
                    
                    // Refresh user data
                    auth()->guard('customer')->user()->refresh();
                    
                    session()->flash('success', 'Update Profil dengan Gambar Berhasil!');
                } else {
                    session()->flash('error', 'Gagal update database');
                }
            } else {
                // Update tanpa gambar baru (tetap gunakan gambar lama)
                $updated = $profile->update([
                    'name'  => $this->name,
                    'email' => $this->email,
                ]);
                
                if ($updated) {
                    session()->flash('success', 'Update Profil Berhasil');
                } else {
                    session()->flash('error', 'Gagal update database');
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Validasi gagal
            session()->flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
        } catch (\Exception $e) {
            // Error lainnya
            session()->flash('error', 'Error: ' . $e->getMessage());
        }

        // redirect to the desired page
        return $this->redirect('/account/my-profile', navigate: true);
    }


    public function render()
    {
        return view('livewire.account.my-profile.index');
    }
}