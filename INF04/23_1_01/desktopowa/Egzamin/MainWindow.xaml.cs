using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows;
using System.Windows.Controls;
using System.Windows.Data;
using System.Windows.Documents;
using System.Windows.Input;
using System.Windows.Media;
using System.Windows.Media.Imaging;
using System.Windows.Navigation;
using System.Windows.Shapes;

namespace Egzamin
{
    /// <summary>
    /// Interaction logic for MainWindow.xaml
    /// </summary>
    public partial class MainWindow : Window
    {
        private string password = "";
        private readonly string alphabet = "abcdefghijklmnopqrstuwvxyz";
        private readonly string digits = "1234567890";
        private readonly string specialCharacters = "!@#$%^&*()_+-=";

        public MainWindow()
        {
            InitializeComponent();
        }

        private void Generate_Click(object sender, RoutedEventArgs e)
        {
            InfoPopup infoWindow = new InfoPopup();
            infoWindow.Width = 200;
            infoWindow.Height = 100;

            this.password = "";
            Random random = new Random();

            int length = int.Parse(PasswordLength.Text);
            bool containsBigLetters = PasswordBigLetters.IsChecked ?? false;
            bool containsDigits = PasswordDigits.IsChecked ?? false;
            bool containsSpecialCharacters = PasswordSpecialCharacters.IsChecked ?? false;

            for(int i = 0; i < length; i++)
            {
                string randomLetter = alphabet[random.Next(0, alphabet.Length)].ToString();
                if (containsBigLetters && i == 0)
                {
                    this.password += randomLetter.ToUpper();
                }
                else if(containsDigits && i == 1)
                {
                    this.password += digits[random.Next(0, digits.Length)];
                }
                else if(containsSpecialCharacters && i == 2)
                {
                    this.password += specialCharacters[random.Next(0, specialCharacters.Length)];
                }
                else
                {
                    this.password += randomLetter;
                }
            }

            infoWindow.Info.Content = this.password;
            infoWindow.Show();
        }

        private void Submit_Click(object sender, RoutedEventArgs e)
        {
            InfoPopup infoWindow = new InfoPopup();
            infoWindow.Width = 500;
            infoWindow.Height = 100;
            infoWindow.Info.Width = 400;

            string position = ((ComboBoxItem)this.Position.SelectedItem).Content.ToString();


            infoWindow.Info.Content = $"Dane pracownika: {this.Name.Text} {this.Surname.Text} {position} Hasło: {this.password}";
            infoWindow.Show();
        }
    }
}
